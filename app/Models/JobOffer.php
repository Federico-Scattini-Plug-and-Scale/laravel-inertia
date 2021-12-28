<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS_ACTIVE = 'active';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CART = 'cart';

    public function jobOfferType()
    {
        return $this->belongsTo(JobOfferType::class);
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

    public static function getByUser($userId, $locale = 'it', $pager = 10)
    {
        return self::
            with(['orders' => function($query)
            {
                $query->orderBy('created_at', 'desc');
            }, 'jobOfferType' => function($query)
            {
                $query->select('id', 'name', 'is_free');
            }])
            ->where('company_id', $userId)
            ->where('locale', $locale)
            ->orderBy('created_at', 'desc')
            ->paginate($pager);
    }

    public static function getActive()
    {
        return self::
            where('status', JobOffer::STATUS_ACTIVE)
            ->select('id', 'status', 'published_at')
            ->get();
    }
}
