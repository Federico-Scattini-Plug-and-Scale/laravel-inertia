<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class JobOfferHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array'
    ];

    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class);
    }

    public static function getLastStatusById($jobOfferId)
    {
        $result = self::
                    orderBy('created_at', 'desc')
                    ->where('job_offer_id', $jobOfferId)
                    ->where('data->status', '!=', JobOffer::STATUS_ARCHIVED)
                    ->select('data')
                    ->first();
        
        return Arr::get($result->data, 'status');
    }
}
