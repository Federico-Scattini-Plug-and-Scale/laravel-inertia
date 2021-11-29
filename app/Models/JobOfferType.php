<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOfferType extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean'
    ];

    public function job_offers() 
    {
        return $this->belongsToMany(JobOffer::class);
    }

    public static function getAllPaginated($pager = 10)
    {
        return self::orderBy('updated_at', 'desc')->paginate($pager);
    }
}
