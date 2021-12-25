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

    public static function getAllPaginated($pager = 10, $locale = 'it')
    {
        return self::
            where('locale', $locale)
            ->orderBy('updated_at', 'desc')
            ->paginate($pager);
    }

    public static function getOptions($locale = 'it')
    {
        return self::
            where('locale', $locale)
            ->where('is_active', true)
            ->orderBy('price', 'desc')
            ->select('id as value', 'name as label')
            ->get();
    }
}
