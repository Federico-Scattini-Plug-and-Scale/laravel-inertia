<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagGroup extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    const GROUP_TYPE_SECTOR = 'group_sector';

    public function tags()
    {
        return $this->hasMany(Tag::class)->orderBy('position');
    }

    public static function getAll($locale = 'it')
    {
        return self::
            where('locale', $locale)
            ->orderBy('position')
            ->get();
    }
}
