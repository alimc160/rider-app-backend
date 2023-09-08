<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface VehicleTypeInterface
{
    public function create(array $attributes): Model;
}
