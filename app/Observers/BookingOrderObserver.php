<?php

namespace App\Observers;

use App\Http\Requests\Booking\UpdateRequest;
use App\Interfaces\BookingStatusLogInterface;
use App\Models\BookingOrder;
use Illuminate\Support\Str;

class BookingOrderObserver
{
    private BookingStatusLogInterface $booking_status_log;
    public function __construct(BookingStatusLogInterface $booking_status_log)
    {
        $this->booking_status_log = $booking_status_log;
    }

    /**
     * Handle the BookingOrder "created" event.
     *
     * @param BookingOrder $booking_order
     * @return void
     */
    public function created(BookingOrder $booking_order)
    {
//        dd('observer',$booking_order->toArray());
    }

    /**
     * Handle the BookingOrder "updated" event.
     *
     * @param BookingOrder $bookingOrder
     * @return void
     */
    public function updated(BookingOrder $bookingOrder)
    {
//        $request_data = request()->all();
//        dd($request_data);
//        $this->booking_status_log->create([
//            'booking_order_id' => $booking_order->id,
//            'status' => $request_data['operation'],
//            'lat' => $request_data['lat'],
//            'long' => $request_data['long'],
//        ]);
    }

    /**
     * Handle the BookingOrder "deleted" event.
     *
     * @param BookingOrder $bookingOrder
     * @return void
     */
    public function deleted(BookingOrder $bookingOrder)
    {
        //
    }

    /**
     * Handle the BookingOrder "restored" event.
     *
     * @param BookingOrder $bookingOrder
     * @return void
     */
    public function restored(BookingOrder $bookingOrder)
    {
        //
    }

    /**
     * Handle the BookingOrder "force deleted" event.
     *
     * @param BookingOrder $bookingOrder
     * @return void
     */
    public function forceDeleted(BookingOrder $bookingOrder)
    {
        //
    }

    /**
     * @param BookingOrder $booking_order
     * @return void
     */
//    public function updating(BookingOrder $booking_order)
//    {
//        $request_data = request()->all();
//        dd($request_data);
//        $this->booking_status_log->create([
//            'booking_order_id' => $booking_order->id,
//            'status' => $request_data['operation'],
//            'lat' => $request_data['lat'],
//            'long' => $request_data['long'],
//        ]);
//    }
}
