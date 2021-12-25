<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobOffer()
    {
        return $this->hasOne(JobOffer::class);
    }

    public static function getByStripeId($stripeId)
    {
        return self::where('stripe_id', $stripeId)->first();
    }
}
