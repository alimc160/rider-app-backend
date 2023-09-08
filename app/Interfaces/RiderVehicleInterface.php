<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface RiderVehicleInterface
{
    public function updateOrCreate(array $exiting_attributes,array $update_attributes): Model;
}
