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

    public static function getOptions($locale = 'it', $withoutFreeTrials = true)
    {
        $query = self::
            where('locale', $locale)
            ->where('is_active', true);

        if ($withoutFreeTrials)
        {
            $query->where('is_free', false);
        }

        return $query
                ->orderBy('price')
                ->select('id', 'name', 'price', 'currency', 'is_free')
                ->get();
    }

    public static function getMoreExpensivePackages($price, $locale = 'it')
    {
        return self::
            where('locale', $locale)
            ->where('is_free', '!=', true)
            ->where('is_active', true)
            ->where('price', '>', $price)
            ->orderBy('price')
            ->select('id', 'name', 'price', 'currency')
            ->get();
    }

    public static function getById($id)
    {
        return self::find($id);
    }

    public function calculateUpgradePrice(JobOffer $jobOffer)
    {
        if ($jobOffer->jobOfferType->is_free)
        {
            return $this->price;
        }

        $pastDays = $jobOffer->validity_days - $jobOffer->getValidityDays();
        $nrOfPackages = $jobOffer->validity_days / 30;
        $pricePerDay = ($jobOffer->jobOfferType->price * $nrOfPackages) / $jobOffer->validity_days;
        $totPriceSpent = $nrOfPackages * $jobOffer->jobOfferType->price;
        $priceSpentPastPeriod = $pricePerDay * $pastDays; 
        return  number_format(($this->price * $nrOfPackages) - ($totPriceSpent - $priceSpentPastPeriod), 0);
    }
}