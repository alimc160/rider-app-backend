<?php

namespace App\Providers;

use App\Models\BookingOrder;
use App\Models\VehicleCategory;
use App\Models\VehicleCompany;
use App\Models\VehicleType;
use App\Observers\BookingOrderObserver;
use App\Observers\VehicleCategoryObserver;
use App\Observers\VehicleCompanyObserver;
use App\Observers\VehicleTypeObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
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
        VehicleCompany::observe(VehicleCompanyObserver::class);
        VehicleType::observe(VehicleTypeObserver::class);
        VehicleCategory::observe(VehicleCategoryObserver::class);
        BookingOrder::observe(BookingOrderObserver::class);
    }
}
