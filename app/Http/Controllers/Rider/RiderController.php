<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Services\RiderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiderController extends Controller
{
    private RiderService $rider_service;
    public function __construct(RiderService $rider_service)
    {
        $this->rider_service = $rider_service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateOnlineStatus(Request $request)
    {
        $request->validate([
            'is_active' => 'required|boolean',
            'lat' => 'required_if:is_active,1|numeric',
            'long' => 'required_if:is_active,1|numeric'
        ],[
            'is_active.required' => 'The status field is required.',
            'lat.required_if' => 'The lat field is required.',
            'long.required_if' => 'The long field is required.',
        ]);
        $rider = $request->user();
        $input = $request->only(['is_active','lat','long']);
        $this->rider_service->updateDeliveryStatus($input,$rider);
        return $this->successResponse('Delivery status updated successfully.');
    }
}
