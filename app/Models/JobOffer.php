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
        return $this->hasOne(JobOfferType::class, 'id', 'job_offer_type_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
