<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class JobOffer extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS_ACTIVE = 'active';
    const STATUS_UNDER_APPROVAL = 'under approval';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CART = 'cart';
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

    public static function boot()
    {
        parent::boot();

        self::deleting(function(JobOffer $jobOffer)
        {
            $jobOffer->tags()->detach();
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
}
