<?php

namespace App\Services;

use App\Http\Traits\ApiResponserTrait;
use App\Interfaces\BookingOrderInterface;
use App\Interfaces\BookingOrderPackageInterface;
use App\Interfaces\RiderRepositoryInterface;
use App\Repositories\BookingOrderRepository;
use App\Repositories\RiderRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;

class BookingOrdersService extends BaseService
{
    private BookingOrderInterface $booking_order_repository;
    private BookingOrderPackageInterface $booking_order_package_repository;
    private RiderRepositoryInterface $rider_repository;
    private GoogleMapApiService $google_map_api_service;
    private RabbitMQService $rabbit_mq_service;

    public function __construct(
        BookingOrderInterface        $booking_order_repository,
        BookingOrderPackageInterface $booking_order_package_repository,
        RiderRepositoryInterface     $rider_repository,
        GoogleMapApiService          $google_map_api_service,
        RabbitMQService              $rabbit_mq_service
    )
    {
        $this->booking_order_repository = $booking_order_repository;
        $this->booking_order_package_repository = $booking_order_package_repository;
        $this->rider_repository = $rider_repository;
        $this->google_map_api_service = $google_map_api_service;
        $this->rabbit_mq_service = $rabbit_mq_service;
    }

    /**
     * @param $request_data
     * @return mixed
     */
    public function createBookingOrder($request_data)
    {
        $relations = [];
        $selected_columns = [
            'id',
            'uuid',
            'queue_name'
        ];
        $pick_up_location = $this->google_map_api_service->getLocationFromLatLng($request_data['pickup_lat'], $request_data['pickup_long']);
        $drop_off_location = $this->google_map_api_service->getLocationFromLatLng($request_data['drop_off_lat'], $request_data['drop_off_long']);
        $get_distance_time = $this->google_map_api_service->distanceMatrix($pick_up_location, $drop_off_location);
        $booking_data = [
            "order_id" => $request_data['order_id'],
            "cargo_booking_id" => $request_data['cargo_booking_id'],
            "pickup_location" => $pick_up_location,
            "pickup_lat" => $request_data['pickup_lat'],
            "pickup_long" => $request_data['pickup_long'],
            "drop_off_location" => $drop_off_location,
            "drop_off_lat" => $request_data['drop_off_lat'],
            "drop_off_long" => $request_data['drop_off_long'],
            "booking_amount" => $request_data['booking_amount'],
            "agent_name" => $request_data['agent_name'],
            "agent_contact" => $request_data['agent_contact'],
            "customer_name" => $request_data['customer_name'],
            "customer_contact" => $request_data['customer_contact'],
            "customer_cnic" => $request_data['customer_cnic'],
            "notes" => $request_data['notes'] ?? null,
            "no_of_packages" => $request_data['no_of_packages']
        ];
        $booking_order = null;
        try {
            $booking_order = $this->booking_order_repository->create($booking_data);
        } catch (Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
        $booking_order->refresh();
        $radius = 5;
        $get_riders_by_haversine = $this->rider_repository->all(
            [
                [
                    'is_active', '=', 1
                ],
                [
                    'status', '=', 'approved'
                ],
                [
                    'ride_available_status', '=', 1
                ],
            ],
            [],
            [
                'distance' => 'ASC'
            ],
            $relations,
            $selected_columns,
            true,
            $radius,
            $request_data['pickup_lat'],
            $request_data['pickup_long']
        );
        $booking_data = array_merge($get_distance_time ?? [], $booking_order->toArray());
        if ($get_riders_by_haversine->count() > 0) {
            foreach ($get_riders_by_haversine as $item) {
                $msg = json_encode($booking_data, true);
                $this->rabbit_mq_service->sendRabbitMQ('default', $item->queue_name, $msg);
            }
        }
        return $booking_data;
    }

    /**
     * @param $booking_order
     * @param $request_data
     * @return mixed
     */
    public function updateBookingOrderData($booking_order, $request_data)
    {
        $this->booking_order_repository->updateBookingOrder($booking_order, $request_data);
        return $this->booking_order_repository->addBookingStatusLog([
            'booking_order_id' => $booking_order->id,
            'status' => $request_data['status'],
            'lat' => $request_data['lat'],
            'long' => $request_data['long'],
        ]);
    }

    /**
     * @param $from_lat
     * @param $from_long
     * @param $to_lat
     * @param $to_long
     * @param int $shortest_distance
     * @return bool
     */
    public function checkDistanceFromLocation($from_lat, $from_long, $to_lat, $to_long, int $shortest_distance = 100)
    {
        $distance = haversineGreatCircleDistance(
            $from_lat,
            $from_long,
            $to_lat,
            $to_long
        );
        if ($distance > $shortest_distance) {
            $this->errorResponse('You cannot change the status because you are far away from destination.');
        }
        return true;
    }

    /**
     * @param $request_data
     * @return mixed
     */
    public function getItem($request_data)
    {
        $booking_order = $this->booking_order_repository->getData([
            ['uuid', '=', $request_data['booking_uuid']]
        ],[],[],[
            'bookingLogs'
        ])->first();
        if (!$booking_order) {
            $this->errorResponse('Booking not found.', 404);
        }
        return $booking_order;
    }

    /**
     * @param $rider
     * @param array $request_data
     * @return mixed
     */
    public function updateAcceptBookingData($rider, array $request_data = [])
    {
        $booking_operation = $request_data['operation'];
        $booking_order = $this->getItem($request_data);
        if ($booking_order->status === $booking_operation && $booking_order->rider_id !== null) {
            $this->errorResponse('Ride already assigned to this booking.');
        } elseif ($booking_order->status === "package_delivered") {
            $this->errorResponse('This order has already delivered.');
        }
        $this->updateBookingOrderData($booking_order, [
            'status' => $booking_operation,
            'rider_id' => $rider->id,
            'booking_order_id' => $booking_order->id,
            'lat' => $request_data['lat'],
            'long' => $request_data['long']
        ]);
        $this->rider_repository->updateRider($rider, [
            'lat' => $request_data['lat'],
            'long' => $request_data['long'],
            'ride_available_status' => 0,
        ]);
        return $booking_order;
    }

    /**
     * @param $rider
     * @param array $request_data
     * @return mixed
     */
    public function updateArrivedForPickBookingData($rider, array $request_data = [])
    {
        $booking_order = $this->getItem($request_data);
        $booking_order_pickup_lat = $booking_order->pickup_lat;
        $booking_order_pickup_long = $booking_order->pickup_long;
        $this->checkDistanceFromLocation(
            $request_data['lat'],
            $request_data['long'],
            $booking_order_pickup_lat,
            $booking_order_pickup_long
        );
        $status = $request_data['status'];
        $booking_order_status = $booking_order->status;
        if (in_array($booking_order_status, ['pending'])) {
            $this->errorResponse('Please first accept this order.');
        }
        if ($booking_order_status === $status) {
            $this->errorResponse("You have already arrived on pickup location.");
        } elseif ($booking_order_status === "package_delivered") {
            $this->errorResponse('You have already delivered this order');
        }
        $this->updateBookingOrderData($booking_order, [
            'status' => $status,
            'rider_id' => $rider->id,
            'booking_order_id' => $booking_order->id,
            'lat' => $request_data['lat'],
            'long' => $request_data['long']
        ]);
        return $booking_order;
    }

    /**
     * @param $rider
     * @param array $request_data
     * @return mixed
     */
    public function updatePackageReceivedBookingData($rider, array $request_data = [])
    {
        $booking_order = $this->getItem($request_data);
        $booking_status = $booking_order->status;
        if (in_array($booking_status, ['pending', 'accepted'])) {
            $this->errorResponse('You did not changed the status to arrived on pick up location.');
        }
        if ($booking_status === $request_data['status']) {
            $this->errorResponse("Your ride has already started.");
        } elseif ($booking_status === "package_delivered") {
            $this->errorResponse('You have already delivered this order.');
        }
        $total_booking_packages = $booking_order->orderPackages->count();
        $no_of_packages = $booking_order->no_of_packages;

        if ($total_booking_packages === 0) {
            $this->errorResponse('Please add order packages details.');
        }
        $status = $request_data['status'];
        $this->updateBookingOrderData($booking_order, [
            'status' => $status,
            'rider_id' => $rider->id,
            'booking_order_id' => $booking_order->id,
            'lat' => $request_data['lat'],
            'long' => $request_data['long']
        ]);
        $this->rider_repository->updateRider($rider, [
            'lat' => $request_data['lat'],
            'long' => $request_data['long']
        ]);
        return $booking_order;
    }

    /**
     * @param $rider
     * @param $request_data
     * @return mixed
     */
    public function updateArrivedForDropOffBookingData($rider, $request_data = [])
    {
        $booking_order = $this->getItem($request_data);
        $booking_order_drop_off_lat = $booking_order->drop_off_lat;
        $booking_order_drop_off_long = $booking_order->drop_off_long;
        $booking_status = $booking_order->status;
        if (in_array($booking_status, ['pending', 'accepted', 'arrived_for_pickup'])) {
            $this->errorResponse('You did not start the rider.');
        }
        if ($booking_status === $request_data['status']) {
            $this->errorResponse("You have already arrived on drop off location.");
        }elseif ($booking_status === "package_delivered") {
            $this->errorResponse('You have already delivered this order.');
        }
        $this->checkDistanceFromLocation(
            $request_data['lat'],
            $request_data['long'],
            $booking_order_drop_off_lat,
            $booking_order_drop_off_long
        );
        $this->updateBookingOrderData($booking_order, [
            'status' => $request_data['status'],
            'rider_id' => $rider->id,
            'booking_order_id' => $booking_order->id,
            'lat' => $request_data['lat'],
            'long' => $request_data['long']
        ]);
        return $booking_order;
    }

    /**
     * @param $rider
     * @param $request_data
     * @return mixed
     * @throws Exception
     */
    public function updatePackageDeliveredBookingData($rider, $request_data)
    {
        $booking_order = $this->getItem($request_data);
        $booking_status = $booking_order->status;
        if (in_array($booking_status, ['pending', 'accepted', 'arrived_for_pickup', 'ride_started'])) {
            $this->errorResponse('Please first change the status to arrived on drop off location.');
        }
        if ($booking_status === $request_data['status']) {
            $this->errorResponse("You have already delivered this order.");
        }
        $booking_order_drop_off_lat = $booking_order->drop_off_lat;
        $booking_order_drop_off_long = $booking_order->drop_off_long;
        $this->checkDistanceFromLocation(
            $request_data['lat'],
            $request_data['long'],
            $booking_order_drop_off_lat,
            $booking_order_drop_off_long
        );
        $receiver_pic = asset(fileUploadViaS3($request_data['receiver_pic'], 'booking/' . $booking_order->uuid, 'receiver_pic'));
        $this->updateBookingOrderData($booking_order, [
            'status' => $request_data['status'],
            'receiver_pic' => $receiver_pic,
            'receiver_name' => $request_data['receiver_name'],
            'receiver_cnic' => $request_data['receiver_cnic'],
            'receiver_contact' => $request_data['receiver_contact'],
            'rider_id' => $rider->id,
            'booking_order_id' => $booking_order->id,
            'lat' => $request_data['lat'],
            'long' => $request_data['long']
        ]);
        $this->rider_repository->updateRider($rider, [
            'lat' => $request_data['lat'],
            'long' => $request_data['long'],
            'ride_available_status' => 0,
        ]);
        $first_status_datetime = $booking_order->bookingLogs->first()->updated_at;
        $last_status_datetime = $booking_order->bookingLogs->last()->updated_at;
        $total_time_taken = $last_status_datetime->diff($first_status_datetime)->format('%H:%i:%s');
        return [
            'id' => $booking_order->id,
            'uuid' => $booking_order->uuid,
            'status' =>$booking_order->status,
            'customer_name' => $booking_order->customer_name,
            'time_taken' => $total_time_taken
        ];
    }

    /**
     * @param $request_data
     * @return Model
     * @throws Exception
     */
    public function addOrderPackageDetails($request_data)
    {
        $booking_order = $this->booking_order_repository->getData([
            ['uuid', '=', $request_data['uuid']]
        ])->first();
        if (!$booking_order) {
            $this->serverErrorResponse('Booking order not exists.');
        } elseif ($booking_order->status === "package_delivered") {
            $this->errorResponse('This booking already completed.');
        } elseif ($booking_order->orderPackages->count() === $booking_order->no_of_packages) {
            $this->errorResponse('You cannot add more than ' . $booking_order->no_of_packages . ' order packages.');
        }
        $image_url = fileUploadViaS3($request_data['image'], 'rider/order', 'booking_order');
        return $this->booking_order_package_repository->create([
            'booking_order_id' => $booking_order->id,
            'image' => asset($image_url),
            'weight' => $request_data['weight']
        ]);
    }

    /**
     * @param $rider
     * @param $request_data
     * @return mixed
     */
    public function getBookingOrderDetails($rider, $uuid)
    {
        $booking_order = $this->booking_order_repository->getData(
            [
                [
                    'uuid', '=', $uuid
                ],
                [
                    'rider_id', '=', $rider->id
                ]
            ],
            [],
            [
                'id', 'uuid', 'cargo_booking_id', 'pickup_location', 'pickup_lat',
                'pickup_long', 'drop_off_location', 'drop_off_lat', 'drop_off_long',
                'customer_name', 'customer_contact', 'customer_cnic',
                'notes', 'booking_amount', 'agent_name', 'agent_contact', 'receiver_pic',
                'receiver_name', 'receiver_cnic', 'receiver_contact', 'created_at'
            ],
            [
                'orderPackages', 'bookingLogs'
            ]
        )->first();
        if (!$booking_order) {
            $this->errorResponse('Booking order not exists against given credentials.');
        }
        return $booking_order;
    }

    public function getOrdersListing($rider,$request_data)
    {
        $status = $request_data['status'] ??  'package_delivered';
        $selected_columns = ['id','uuid','order_id','pickup_location','drop_off_location','pickup_lat','pickup_long','drop_off_lat','drop_off_long'];
        $orders = $this->booking_order_repository->getData(
            [
                ['rider_id' ,'=',$rider->id],
                ['status','=',$status]
            ],
            [],
            $selected_columns
        );
        return [
            'items' => $orders->getCollection(),
            'pagination' => paginationData($orders)
        ];
    }

}
