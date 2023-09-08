<?php

namespace App\Http\Controllers;

use App\Services\RiderService;
use Illuminate\Http\Request;

class CityController extends Controller
{
    private RiderService $rider_service;
    public function __construct(RiderService $rider_service)
    {
        $this->rider_service = $rider_service;
    }

    public function citiesList()
    {
        $list = $this->rider_service->getCitiesList();
        return $this->successResponse('',$list);
    }
}
