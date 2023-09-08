<?php

namespace App\Observers;

use App\Http\Traits\ApiResponserTrait;
use App\Models\VehicleCompany;

class VehicleCompanyObserver
{
    use ApiResponserTrait;
    /**
     * Handle the VehicleCompany "created" event.
     *
     * @param VehicleCompany $vehicle_company
     * @return void
     */
    public function created(VehicleCompany $vehicle_company)
    {
        //
    }

    /**
     * Handle the VehicleCompany "updated" event.
     *
     * @param VehicleCompany $vehicle_company
     * @return void
     */
    public function updated(VehicleCompany $vehicle_company)
    {
        //
    }

    /**
     * Handle the VehicleCompany "deleted" event.
     *
     * @param VehicleCompany $vehicle_company
     * @return void
     */
    public function deleted(VehicleCompany $vehicle_company)
    {

    }

    /**
     * Handle the VehicleCompany "restored" event.
     *
     * @param VehicleCompany $vehicle_company
     * @return void
     */
    public function restored(VehicleCompany $vehicle_company)
    {
        //
    }

    /**
     * Handle the VehicleCompany "force deleted" event.
     *
     * @param VehicleCompany $vehicle_company
     * @return void
     */
    public function forceDeleted(VehicleCompany $vehicle_company)
    {
        //
    }

    /**
     * @throws \Exception
     */
    public function deleting(VehicleCompany $vehicle_company)
    {
        if($vehicle_company->vehicleTypes->count() > 0) {
            $this->errorResponse('Delete action cannot be perform on this record.');
        }
    }
}
