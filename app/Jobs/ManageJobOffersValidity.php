<?php

namespace App\Jobs;

use App\Models\JobOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ManageJobOffersValidity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $activeJobOffers = JobOffer::getActive();

        $activeJobOffers->each(function($item) {
            if ($item->published_at < now('Europe/Rome')->subDays($item->validity_days)->endOfDay())
            {
                $item->published_at = null;
                $item->validity_days = 0;
                $item->status = JobOffer::STATUS_INACTIVE;
                $item->save();
            }
        });
    }
}
