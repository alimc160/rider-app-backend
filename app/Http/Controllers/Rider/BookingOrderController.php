<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\AddRequest;
use App\Http\Requests\Booking\PackageDeliveredRequest;
use App\Http\Requests\Booking\UpdateRequest;
use App\Http\Requests\BookingOrderRequest;
use App\Services\BookingOrdersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingOrderController extends Controller
{
    private BookingOrdersService $booking_orders_service;

    public function __construct(BookingOrdersService $booking_orders_service)
    {
        $this->booking_orders_service = $booking_orders_service;
    }

    public function addBookingOrder(AddRequest $request)
    {
        $input = $request->all();
        $data = $this->booking_orders_service->createBookingOrder($input);
        return $this->successResponse("Booking added successfully.", $data);
    }

    public function addBookingOrderPackage(Request $request)
    {
        $request->validate([
            'uuid' => 'required',
            'weight' => 'required',
            'image' => 'required|image|max:1024',
            'no_of_items' => 'required|integer|min:1'
        ],[
            'no_of_items.required' => 'Please enter total number of pieces.',
            'no_of_items.integer' => 'Number of pieces field should be integer value.',
            'no_of_items.min' => 'The Number of pieces must be at least 1.',
        ]);
        $input = $request->all();
        $response = $this->booking_orders_service->addOrderPackageDetails($input);
        return $this->successResponse('Order package details added successfully.',$response);
    }

    public function getBookingOrderDetails($uuid,Request $request)
    {;
        $rider = $request->user();
        $response = $this->booking_orders_service->getBookingOrderDetails($rider,$uuid);
        return $this->successResponse('',$response);
    }

    /**
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function acceptBooking(UpdateRequest $request): JsonResponse
    {
        $request->request->add(['operation'=>'accepted']);
        $input = $request->all();
        $rider = $request->user();
        $this->booking_orders_service->updateAcceptBookingData($rider,$input);
        return $this->successResponse('Booking accepted successfully!');
    }

    /**
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function arrivedForPickupBooking(UpdateRequest $request): JsonResponse
    {
        $request->request->add(['status' => 'arrived_for_pickup']);
        $input = $request->all();
        $rider = $request->user();
        $this->booking_orders_service->updateArrivedForPickBookingData($rider,$input);
        return $this->successResponse('Status change to arrived for pickup successfully!');
    }

    /**
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function packageReceivedBookingOperation(UpdateRequest $request): JsonResponse
    {
        $request->request->add(['status' => 'ride_started']);
        $input = $request->all();
        $rider = $request->user();
        $this->booking_orders_service->updatePackageReceivedBookingData($rider,$input);
        return $this->successResponse('Booking order collected!');
    }

    /**
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function arrivedForDropoffBookingOperation(UpdateRequest $request): JsonResponse
    {
        $request->request->add(['status' => 'arrived_for_drop_off']);
        $input = $request->all();
        $rider = $request->user();
        $this->booking_orders_service->updateArrivedForDropOffBookingData($rider,$input);
        return $this->successResponse('You have arrived on destination location!');
    }

    /**
     * @param PackageDeliveredRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function packageDeliveredBookingOperation(PackageDeliveredRequest $request): JsonResponse
    {
        $request->request->add(['status' => 'package_delivered']);
        $input = $request->all();
        $rider = $request->user();
        $response = $this->booking_orders_service->updatePackageDeliveredBookingData($rider,$input);
        return $this->successResponse('Booking delivered successfully!',$response);
    }

    public function getPastOrdersListing(Request $request)
    {
        $rider = $request->user();
        $listing = $this->booking_orders_service->getOrdersListing($rider,$request->all());
        return $this->successResponse('',$listing);
    }
}
