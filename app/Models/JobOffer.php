<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function job_offer_type()
    {
        return $this->hasOne(JobOfferType::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
