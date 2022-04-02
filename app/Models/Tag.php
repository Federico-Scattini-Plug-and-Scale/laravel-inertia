<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function tagGroup()
    {
        return $this->belongsTo(TagGroup::class);
    }

    public function suggestor()
    {
        return $this->belongsTo(User::class, 'suggested_by', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function jobOffers()
    {
        return $this->belongsToMany(jobOffer::class);
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(function(Tag $tag)
        {
            $tag->users()->detach();
            $tag->jobOffers()->detach();
        });
    }

    public static function getOptionsBasedOnType($type, $userId, $locale = 'it')
    {
        return self::
            whereHas('tagGroup', function($query) use ($type, $locale) {
                return $query
                    ->where('type', $type)
                    ->where('is_active', true)
                    ->where('locale', $locale);
            })
            ->where('locale', $locale)
            ->where(function($query) use ($userId)
            {
                return $query
                    ->where('is_active', true)
                    ->orWhere(function($query) use ($userId)
                    {
                        return $query
                            ->where('is_approved', true)
                            ->orWhere('suggested_by', $userId);
                    });
            })
            ->orderBy('updated_at', 'desc')
            ->select('id as value', 'name as label')
            ->get();
    }

    public static function getByGroup($groupId)
    {
        return self::
            with('suggestor')
            ->where('tag_group_id', $groupId)
            ->orderBy('position')
            ->get();
    }

    public static function getByGroupType($groupType, $locale, $select = ['*'])
    {
        return self::
            whereHas('tagGroup', function ($q) use ($groupType)
            {
                $q->where('type', $groupType);
            })
            ->where('is_active', true)
            ->where('locale', $locale)
            ->orderBy('position')
            ->select($select)
            ->get();
    }
}
