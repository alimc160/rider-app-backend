<?php

namespace App\Services;

use App\Interfaces\RiderVehicleInterface;

class RiderVehicleService extends BaseService
{
    private RiderVehicleInterface $rider_vehicle_repository;
    public function __construct(RiderVehicleInterface $rider_vehicle_repository)
    {
        $this->rider_vehicle_repository = $rider_vehicle_repository;
    }

    /**
     * @param $request_data
     * @param $rider
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addDetails($request_data,$rider)
    {
        $rider_vehicle = $rider->riderVehicle;
        $rider_id = $rider->id;
        $color = $rider_vehicle->color ?? null;
        $image = $rider_vehicle->image ?? null;
        $registration_number = $rider_vehicle->registration_number ?? null;
        return $this->rider_vehicle_repository->updateOrCreate(
            ['rider_id' => $rider_id],
            [
                'rider_id' => $rider_id,
                'vehicle_type_id' => $request_data['vehicle_type_id'],
                'registration_number' => $request_data['registration_number'] ?? $registration_number,
                'image' => $request_data['image'] ?? $image,
                'color' => $request_data['color'] ?? $color,
            ]
        );
    }

    public function updateStatus($request_data)
    {
        $rider_vehicle = $this->rider_vehicle_repository->getData(
            [
                ['id','=',$request_data['rider_vehicle_id']]
            ]
        )->first();
        if (!$rider_vehicle) {
            $this->errorResponse('Record not found.');
        }
        $rider_vehicle->update([
            'status' => $request_data['status'],
            'description' => $request_data['description'] ?? $rider_vehicle->description
        ]);
        return $rider_vehicle;
    }
}
