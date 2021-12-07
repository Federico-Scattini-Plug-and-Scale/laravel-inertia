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

    public static function getAll($locale = 'it')
    {
        return self::
            where('locale', $locale)
            ->orderBy('position')
            ->get();
    }
}
