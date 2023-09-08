<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface BookingOrderInterface
{
    public function updateBookingOrder(object $booking_order,array $attributes): bool;

    public function addBookingStatusLog(array $attributes): Model;
}
