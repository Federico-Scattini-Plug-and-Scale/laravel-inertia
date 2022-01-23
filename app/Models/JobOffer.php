<?php

namespace App\Models;

use App\Extensions\Traits\DraftableModel;
use Carbon\Carbon;
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

    public function history()
    {
        return $this->hasMany(JobOfferHistory::class);
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
                $query->select('id', 'name', 'is_free', 'price');
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

    public function scopeLocation($query, $locations)
    {
        if (empty($locations))
            return $query;

        return $query->where(function($query) use ($locations)
        {
            foreach (explode('-', $locations) as $location)
            {
                $query
                    ->orWhere('country', 'LIKE', "%{$location}%")
                    ->orWhere('city', 'LIKE', "%{$location}%")
                    ->orWhere('region', 'LIKE', "%{$location}%")
                    ->orWhere('province', 'LIKE', "%{$location}%");
            }
        });
    }

    public static function getListing($paginate = 100, $locale = 'it', $category = 'all', $locations = '')
    {
        return self::
                    with(['company.detail:id,name', 'tags:id,name', 'category:id,name'])
                    ->whereHas('category', function ($q) use ($category)
                    {
                        $q->where('name', $category);
                    })
                    ->where('locale', $locale)
                    ->where('status', self::STATUS_ACTIVE)
                    ->location($locations)
                    ->orderBy('created_at')
                    ->paginate($paginate);
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