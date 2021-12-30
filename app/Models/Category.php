<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function jobOffers()
    {
        return $this->hasMany(JobOffer::class);
    }

    public static function getAll($locale = 'it')
    {
        return self::
            where('locale', $locale)
            ->orderBy('position')
            ->get();
    }

    public static function getOptions($locale = 'it')
    {
        return self::
            where('locale', $locale)
            ->where('is_active', true)
            ->orderBy('position')
            ->select('id as value', 'name as label')
            ->get();
    }
}
