<?php

namespace App\Observers;

use App\Models\JobOfferType;

class JobOfferTypeObserver
{
    /**
     * Handle the JobOfferType "created" event.
     *
     * @param  \App\Models\JobOfferType  $jobOfferType
     * @return void
     */
    public function created(JobOfferType $jobOfferType)
    {
        $jobOfferType->locale = app()->getLocale();
        $jobOfferType->saveQuietly();
    }

    /**
     * Handle the JobOfferType "updated" event.
     *
     * @param  \App\Models\JobOfferType  $jobOfferType
     * @return void
     */
    public function updated(JobOfferType $jobOfferType)
    {
        if ($jobOfferType->isDirty())
        {
            setStripeKey();

            if ($jobOfferType->isDirty('stripe_product_name'))
            {
                if (!empty($jobOfferType->stripe_product_id))
                {
                    updateStripeProduct($jobOfferType->stripe_product_id, [
                        'name' => $jobOfferType->stripe_product_name
                    ]);
                }
                else
                {
                    $newStripeProduct = createStripeProduct($jobOfferType->stripe_product_name);
                    $jobOfferType->stripe_product_id = $newStripeProduct->id;
                }
            }

            if ($jobOfferType->isDirty('price') || $jobOfferType->isDirty('currency'))
            {
                if (!empty($jobOfferType->stripe_price_id))
                {
                    $oldStripePrice = retrieveStripePrice($jobOfferType->stripe_price_id);
                    updateStripePrice($oldStripePrice->id, ['active' => false]);

                    $newStripePrice = createStripePrice($jobOfferType->stripe_product_id, number_format(($jobOfferType->price*100), 0, '', ''), $jobOfferType->currency);
                    $jobOfferType->stripe_price_id = $newStripePrice->id;
                }
                else
                {
                    $newStripePrice = createStripePrice($jobOfferType->stripe_product_id, number_format(($jobOfferType->price*100), 0, '', ''), $jobOfferType->currency);
                    $jobOfferType->stripe_price_id = $newStripePrice->id;
                }
            }

            $jobOfferType->saveQuietly();
        }
    }

    /**
     * Handle the JobOfferType "deleted" event.
     *
     * @param  \App\Models\JobOfferType  $jobOfferType
     * @return void
     */
    public function deleted(JobOfferType $jobOfferType)
    {
        setStripeKey();

        if (!empty($jobOfferType->stripe_price_id))
        {
            updateStripePrice($jobOfferType->stripe_price_id, ['active' => false]);
        }

        if (!empty($jobOfferType->stripe_product_id))
        {
            updateStripeProduct($jobOfferType->stripe_product_id, ['active' => false]);
        }
    }

    /**
     * Handle the JobOfferType "restored" event.
     *
     * @param  \App\Models\JobOfferType  $jobOfferType
     * @return void
     */
    public function restored(JobOfferType $jobOfferType)
    {
        //
    }

    /**
     * Handle the JobOfferType "force deleted" event.
     *
     * @param  \App\Models\JobOfferType  $jobOffertType
     * @return void
     */
    public function forceDeleted(JobOfferType $jobOfferType)
    {
        //
    }
}
