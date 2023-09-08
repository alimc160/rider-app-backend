<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Services\RiderVehicleService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RiderVehicleController extends Controller
{
    private RiderVehicleService $rider_vehicle_service;
    public function __construct(RiderVehicleService $rider_vehicle_service)
    {
        $this->rider_vehicle_service = $rider_vehicle_service;
    }

    public function addVehicleDetails(Request $request)
    {
        $request->validate([
            'registration_number' => [
                'required',
                Rule::unique('rider_vehicles')->where(function ($query) use($request){
                    return $query->where('rider_id','!=',$request->user()->id);
                }),
                'max:255'
            ],
            'vehicle_type_id' => 'required'
        ],[
           'registration_number.required' => 'The registration_number field is required.',
           'registration_number.unique' => 'The registration_number has already been taken.',
            'vehicle_type_id.required' => 'The vehicle field is required.'
        ]);
        $input = $request->only(['registration_number','vehicle_type_id','image','color']);
        $rider = $request->user();
        $data = $this->rider_vehicle_service->addDetails($input,$rider);
        return $this->successResponse('Vehicle details added successfully!',$data);
    }
}
