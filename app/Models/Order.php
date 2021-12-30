<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    const INVOICE_PREFIX = 'A7';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class);
    }

    public static function getByStripeId($stripeId)
    {
        return self::where('stripe_id', $stripeId)->first();
    }

    public static function getLastInvoiceNumber()
    {
        return self::orderBy('created_at')->where('invoice_number', '!=', null)->first();
    }
}
