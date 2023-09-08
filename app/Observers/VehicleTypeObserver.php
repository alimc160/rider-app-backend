<?php

namespace App\Observers;

use App\Http\Traits\ApiResponserTrait;
use App\Models\VehicleType;

class VehicleTypeObserver
{
    use ApiResponserTrait;
    /**
     * Handle the VehicleType "created" event.
     *
     * @param  \App\Models\VehicleType  $vehicleType
     * @return void
     */
    public function created(VehicleType $vehicleType)
    {
        //
    }

    /**
     * Handle the VehicleType "updated" event.
     *
     * @param  \App\Models\VehicleType  $vehicleType
     * @return void
     */
    public function updated(VehicleType $vehicleType)
    {
        //
    }

    /**
     * Handle the VehicleType "deleted" event.
     *
     * @param  \App\Models\VehicleType  $vehicleType
     * @return void
     */
    public function deleted(VehicleType $vehicleType)
    {
        //
    }

    /**
     * Handle the VehicleType "restored" event.
     *
     * @param  \App\Models\VehicleType  $vehicleType
     * @return void
     */
    public function restored(VehicleType $vehicleType)
    {
        //
    }

    /**
     * Handle the VehicleType "force deleted" event.
     *
     * @param  \App\Models\VehicleType  $vehicleType
     * @return void
     */
    public function forceDeleted(VehicleType $vehicleType)
    {
        //
    }

    public function deleting(VehicleType $vehicle_type)
    {
        if($vehicle_type->riderVehicles->count() > 0) {
            $this->errorResponse('Delete action cannot be perform on this record.');
        }
    }
}
