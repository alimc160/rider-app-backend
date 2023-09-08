<?php

namespace App\Observers;

use App\Http\Traits\ApiResponserTrait;
use App\Models\VehicleCategory;

class VehicleCategoryObserver
{
    use ApiResponserTrait;
    /**
     * Handle the VehicleCategory "created" event.
     *
     * @param VehicleCategory $vehicleCategory
     * @return void
     */
    public function created(VehicleCategory $vehicleCategory)
    {
        //
    }

    /**
     * Handle the VehicleCategory "updated" event.
     *
     * @param VehicleCategory $vehicleCategory
     * @return void
     */
    public function updated(VehicleCategory $vehicleCategory)
    {
        //
    }

    /**
     * Handle the VehicleCategory "deleted" event.
     *
     * @param VehicleCategory $vehicleCategory
     * @return void
     */
    public function deleted(VehicleCategory $vehicleCategory)
    {
        //
    }

    /**
     * Handle the VehicleCategory "restored" event.
     *
     * @param VehicleCategory $vehicleCategory
     * @return void
     */
    public function restored(VehicleCategory $vehicleCategory)
    {
        //
    }

    /**
     * Handle the VehicleCategory "force deleting" event.
     *
     * @param VehicleCategory $vehicle_category
     * @return void
     */
    public function deleting(VehicleCategory $vehicle_category)
    {
        if($vehicle_category->vehicleTypes->count()) {
            $this->errorResponse('Delete action cannot be perform on this record.');
        }
    }
}
