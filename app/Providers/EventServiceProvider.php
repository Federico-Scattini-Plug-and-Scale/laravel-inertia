<?php

namespace App\Providers;

use App\Models\JobOffer;
use App\Models\JobOfferType;
use App\Models\Order;
use App\Observers\JobOfferObserver;
use App\Observers\JobOfferTypeObserver;
use App\Observers\OrderObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        JobOfferType::observe(JobOfferTypeObserver::class);
        Order::observe(OrderObserver::class);
        JobOffer::observe(JobOfferObserver::class);
    }
}
