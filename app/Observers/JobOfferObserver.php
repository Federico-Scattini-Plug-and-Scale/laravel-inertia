<?php

namespace App\Observers;

use App\Models\JobOffer;
use App\Models\JobOfferHistory;

class JobOfferObserver
{
    /**
     * Handle the JobOffer "created" event.
     *
     * @param  \App\Models\JobOffer  $jobOffer
     * @return void
     */
    public function created(JobOffer $jobOffer)
    {
        JobOfferHistory::create([
            'data' => $jobOffer,
            'job_offer_id' => $jobOffer->id
        ]);
    }

    /**
     * Handle the JobOffer "updated" event.
     *
     * @param  \App\Models\JobOffer  $jobOffer
     * @return void
     */
    public function updated(JobOffer $jobOffer)
    {
        JobOfferHistory::create([
            'data' => $jobOffer,
            'job_offer_id' => $jobOffer->id
        ]);
    }

    /**
     * Handle the JobOffer "deleted" event.
     *
     * @param  \App\Models\JobOffer  $jobOffer
     * @return void
     */
    public function deleted(JobOffer $jobOffer)
    {
        //
    }

    /**
     * Handle the JobOffer "restored" event.
     *
     * @param  \App\Models\JobOffer  $jobOffer
     * @return void
     */
    public function restored(JobOffer $jobOffer)
    {
        //
    }

    /**
     * Handle the JobOffer "force deleted" event.
     *
     * @param  \App\Models\JobOffer  $jobOffer
     * @return void
     */
    public function forceDeleted(JobOffer $jobOffer)
    {
        //
    }
}
