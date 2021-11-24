<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOfferType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function job_offers() 
    {
        return $this->belongsToMany(JobOffer::class);
    }
}
