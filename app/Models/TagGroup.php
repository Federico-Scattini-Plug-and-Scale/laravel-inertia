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
    const GROUP_TYPE_INDUSTRY = 'group_industry';
    const GROUP_TYPE_LANGUAGE = 'group_language';
    const GROUP_TYPE_PROCESS_TYPE = 'group_type_of_processing';
    const GROUP_TYPE_MACHINE_TYPE = 'group_type_of_machine';
    const GROUP_TYPE_MACHINE = 'group_machine';
    const GROUP_TYPE_TECH_SKILLS= 'group_extra_tech_skills';
    const GROUP_TYPE_EXP = 'group_exp';
    const GROUP_TYPE_CONTRACT = 'group_contract_type';

    public function tags()
    {
        return $this->hasMany(Tag::class)->orderBy('position');
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(function(TagGroup $group)
        {
            $group->tags->each(function($tag)
            {
                $tag->users()->detach();
                $tag->jobOffers()->detach();
            });
        });
    }

    public static function getAll($locale = 'it')
    {
        return self::
            where('locale', $locale)
            ->orderBy('position')
            ->get();
    }

    public static function getByType($type, $locale)
    {
        return self::
            where('locale', $locale)
            ->where('type', $type)
            ->firstOrFail();
    }
}
