<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOfferApiError extends Model
{
    use HasFactory;

    protected $guarded = [];

	protected $casts = [
		'errors' => 'array',
	];

    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class);
    }
}
