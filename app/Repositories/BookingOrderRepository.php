<?php

namespace App\Repositories;

use App\Interfaces\BookingOrderInterface;
use App\Models\BookingOrder;
use App\Models\BookingStatusLog;
use Illuminate\Database\Eloquent\Model;

class BookingOrderRepository extends BaseRepository implements BookingOrderInterface
{
    private BookingStatusLog $booking_status_log;
    public function __construct(BookingOrder $model,BookingStatusLog $booking_status_log)
    {
        parent::__construct($model);
        $this->booking_status_log = $booking_status_log;
    }

    /**
     * @param object $booking_order
     * @param array $attributes
     * @return bool
     */
    public function updateBookingOrder(object $booking_order, array $attributes): bool
    {
        return $booking_order->update($attributes);
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function addBookingStatusLog(array $attributes): Model
    {
        return $this->booking_status_log->create($attributes);
    }
}
