<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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

    public static function getByUser($userId, $paginate = 20, $filters = [])
    {
        return self
                    ::where('user_id', $userId)
                    ->invoiceNumber(Arr::get($filters, 'number', ''))
                    ->date(Arr::get($filters, 'date', ''))
                    ->orderBy('created_at', 'desc')
                    ->paginate($paginate);
    }

    public function scopeInvoiceNumber($query, $invoiceNumber)
    {
        if (empty($invoiceNumber))
        {
            return $query;
        }
        return $query->where('invoice_number', $invoiceNumber);
    }

    public function scopeDate($query, $date)
    {
        if (empty($date))
        {
            return $query;
        }
        return $query->whereDate('created_at', $date);
    }
}
