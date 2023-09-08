<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rider\UpdateVehicleStatusRequest;
use App\Services\RiderService;
use App\Services\RiderVehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiderController extends Controller
{
    private RiderService $rider_service;
    private RiderVehicleService $ride_vehicle_service;

    public function __construct(RiderService $rider_service,RiderVehicleService $ride_vehicle_service)
    {
        $this->rider_service = $rider_service;
        $this->ride_vehicle_service = $ride_vehicle_service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $input = $request->all();
        $data = $this->rider_service->getAllRiders($input);
        return $this->successResponse('',$data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getRiderDetails(Request $request)
    {
        $request->validate([
            'uuid' => 'required'
        ]);
        $input = $request->only(['uuid']);
        $data = $this->rider_service->getItem($input);
        return $this->successResponse('',$data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'uuid' => 'required',
            'status' => 'required|in:approved,cancelled,in_progress'
        ]);
        $input = $request->only(['uuid','status']);
        $this->rider_service->updateRiderStatus($input);
        return $this->successResponse('Rider status updated successfully.');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateFileStatus(Request $request)
    {
        $request->validate([
            'attribute' => 'required|in:licence,cnic,contract,selfie_picture',
            'status' => 'required|in:in_progress,approved,cancelled',
            'description' => 'sometimes|required|max:250',
            'attribute_id' => 'required|integer'
        ]);
        $input = $request->all();
        $this->rider_service->editFileStatus($input);
        return $this->successResponse('File status updated successfully.');
    }

    /**
     * @param UpdateVehicleStatusRequest $request
     * @return JsonResponse
     */
    public function updateRiderVehicleStatus(UpdateVehicleStatusRequest $request)
    {
        $input = $request->all();
        $this->ride_vehicle_service->updateStatus($input);
        return $this->successResponse('Status updated successfully.');
    }
}
