<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class JobOffer extends Model
{
    use HasFactory, SoftDeletes, DraftableModel;

    protected $guarded = [];

    const STATUS_ACTIVE = 'active';
    const STATUS_UNDER_APPROVAL = 'under approval';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CART = 'cart';
    const STATUS_ARCHIVED = 'archived';
    const VALIDITY = 30;

    public function jobOfferType()
    {
        return $this->belongsTo(JobOfferType::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(function(JobOffer $jobOffer)
        {
            if ($jobOffer->isForceDeleting())
            {
                $jobOffer->tags()->detach();
            }
            else
            {
                $jobOffer->status = self::STATUS_ARCHIVED;
                $jobOffer->save();
            }
        });
    }

    public static function getByUser($userId, $locale = 'it', $pager = 10, $filters = [])
    {
        $query = self::
            with(['orders' => function($query)
            {
                $query->orderBy('created_at', 'desc');
            }, 'jobOfferType' => function($query)
            {
                $query->select('id', 'name', 'is_free');
            }])
            ->where('company_id', $userId)
            ->where('locale', $locale);

        if (!empty($filters))
        {
            $query = self::filters($query, $filters);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($pager);
    }

    public static function getForAdmin($locale = 'it', $pager = 10, $filters = [])
    {
        $query = self::
            withTrashed()
            ->with(['orders' => function($query)
            {
                $query->orderBy('created_at', 'desc');
            }, 'jobOfferType' => function($query)
            {
                $query->select('id', 'name', 'is_free');
            }])
            ->where('locale', $locale);

        if (!empty($filters))
        {
            $query = self::filters($query, $filters);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($pager);
    }

    public static function getActive()
    {
        return self::
            where('status', JobOffer::STATUS_ACTIVE)
            ->select('id', 'status', 'published_at', 'validity_days')
            ->get();
    }

    public static function getStatusOptions()
    {
        return [
            [
                'value' => JobOffer::STATUS_ACTIVE,
                'label' => __(JobOffer::STATUS_ACTIVE)
            ],
            [
                'value' => JobOffer::STATUS_INACTIVE,
                'label' => __(JobOffer::STATUS_INACTIVE)
            ],
            [
                'value' => JobOffer::STATUS_UNDER_APPROVAL,
                'label' => __(JobOffer::STATUS_UNDER_APPROVAL)
            ],
            [
                'value' => JobOffer::STATUS_CART,
                'label' => __(JobOffer::STATUS_CART)
            ],
            [
                'value' => JobOffer::STATUS_UNDER_APPROVAL,
                'label' => __(JobOffer::STATUS_UNDER_APPROVAL)
            ],
            [
                'value' => JobOffer::STATUS_ARCHIVED,
                'label' => __(JobOffer::STATUS_ARCHIVED)
            ]
        ];
    }

    private static function filters($query, $filters)
    {
        if (Arr::has($filters, 'title') && !empty(Arr::get($filters, 'title')))
        {
            $query->where('title', 'like', '%' . Arr::get($filters, 'title') . '%');
        }

        if (Arr::has($filters, 'status') && !empty(Arr::get($filters, 'status')))
        {
            $query->where('status', Arr::get($filters, 'status'));
        }

        return $query;
    }

    public function getValidityDays()
    {
        return now('Europe/Rome')->subDays($this->validity_days)->endOfDay()->diffInDays(Carbon::parse($this->published_at, 'Europe/Rome'), false);
    }
}

trait DraftableModel
{
    private static $userID;

    /**
     * get All Drafts Collection for model
     * @param bool $unfillable
     * @return Collection
     */
    public static function getAllDrafts($unfillable = false)
    {

        $draftsEnteries = static::DraftsQuery()->get();

        return static::getDraftsCollection($draftsEnteries, $unfillable);
    }


    /**
     * get All Published Drafts Collection for model
     * @param bool $unfillable
     * @return Collection
     */
    public static function getPublishedDraft($unfillable = false)
    {
        $draftsEnteries = static::DraftsQuery()->Published()->get();

        return static::getDraftsCollection($draftsEnteries, $unfillable);
    }


    /**
     * get All UnPublished Drafts Collection for model
     * @param bool $unfillable
     * @return Collection
     */
    public static function getUnPublishedDraft($unfillable = false)
    {
        $draftsEnteries = static::DraftsQuery()->UnPublished()->get();
        
        return static::getDraftsCollection($draftsEnteries, $unfillable);
    }


    /**
     * Get Drafts Collection
     * @param $draftsEnteries
     * @param $unfillable
     * @return Collection
     */
    private static function getDraftsCollection($draftsEnteries, $unfillable)
    {
        return static::buildCollection($draftsEnteries, $unfillable);
    }


    /**
     * Save model as draft
     * @return $this
     */
    public function saveAsDraft()
    {
        $draftableArray = $this->toArray();
        $draftableEnteryArray = ['draftable_id' => $this->id, 'draftable_data' => $draftableArray, 'draftable_model' => static::class, 'published_at' => null,'user_id'=>static::$userID,'data'=>[]];

        try {
            $draft = Draftable::create($draftableEnteryArray);
            $this->draft=$draft;
        } catch (\Exception $e) {
            throw new  Exception($e->getMessage());
        }

        return $this;
    }


    /**
     * Save model with draft
     * @return $this
     */
    public function saveWithDraft()
    {
        $this->save();
        $draftableArray = $this->toArray();
        unset($draftableArray['id']);
        $draftableEnteryArray = ['draftable_id' => $this->id, 'draftable_data' => $draftableArray, 'draftable_model' => static::class, 'published_at' => Carbon::now(),'user_id'=>static::$userID,'data'=>[]];

        try {
            $draft = Draftable::create($draftableEnteryArray);
            $this->draft=$draft;
        } catch (\Exception $e) {
            throw new  Exception($e->getMessage());
        }

        return $this;
    }


    /**
     * Build Collection for model
     * @param $draftsEnteries
     * @param bool $unfillable
     * @return Collection
     */
    private static function buildCollection($draftsEnteries, $unfillable = false)
    {
        if ($unfillable) return $draftsEnteries;

        $collection = new Collection();

        foreach ($draftsEnteries as $entery) {
            $new_class = new static();
            $new_class->forceFill($entery->draftable_data);
            $new_class->published_at = $entery->published_at;
            $new_class->draft = $entery;
            $collection->push($new_class);
        }

        return $collection;
    }


    /**
     * Drafts Main Query
     * @return mixed
     */
    private static function DraftsQuery()
    {
        $userQuery=[];

        if(Static::$userID!=null) $userQuery['user_id'] = Static::$userID;

        return Draftable::where('draftable_model', static::class)->where($userQuery);
    }


    /**
     * Publish unpublished draft
     * @return $this
     */
    public function publish()
    {
        if (is_null($this->published_at)) {
            $this->draft->publish();
        }

        return $this;
    }


    /**
     * Drafts morph relation
     * @return mixed
     */
    public function drafts()
    {
        return $this->morphMany(Draftable::class, 'draftable', 'draftable_model', 'draftable_id');
    }


    /**
     * get draft by id
     * @param $id
     * @return mixed
     */
    public function getDraft($id)
    {
        $draftsEnteries = static::DraftsQuery()->where('id', $id)->first();

        return $draftsEnteries;
    }


    /**
     * Set user for draft ( the creator of draft )
     * @param $user
     * @return DraftableModel
     */
    public static  function setUser($user)
    {
        static::$userID = $user->id;

        return new static();
    }
}