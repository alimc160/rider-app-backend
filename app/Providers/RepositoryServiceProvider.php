<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Interfaces\BaseRepositoryInterface',
            'App\Repositories\BaseRepository'
        );
        $this->app->bind(
            'App\Interfaces\RiderRepositoryInterface',
            'App\Repositories\RiderRepository'
        );
        $this->app->bind(
            'App\Interfaces\VehicleTypeInterface',
            'App\Repositories\VehicleTypeRepository'
        );
        $this->app->bind(
            'App\Interfaces\RoleInterface',
            'App\Repositories\RoleRepository'
        );
        $this->app->bind(
            'App\Interfaces\RiderVehicleInterface',
            'App\Repositories\RiderVehicleRepository'
        );
        $this->app->bind(
            'App\Interfaces\VehicleCategoryInterface',
            'App\Repositories\VehicleCategoryRepository'
        );
        $this->app->bind(
            'App\Interfaces\VehicleCompanyInterface',
            'App\Repositories\VehicleCompanyRepository'
        );
        $this->app->bind(
            'App\Interfaces\BookingOrderInterface',
            'App\Repositories\BookingOrderRepository'
        );
        $this->app->bind(
            'App\Interfaces\BookingOrderPackageInterface',
            'App\Repositories\BookingOrderPackageRepository'
        );
        $this->app->bind(
            'App\Interfaces\BookingStatusLogInterface',
            'App\Repositories\BookingStatusLogRepository'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
