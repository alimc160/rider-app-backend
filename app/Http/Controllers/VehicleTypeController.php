<?php

namespace App\Http\Controllers;

use App\Services\VehicleTypeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VehicleTypeController extends Controller
{
    private VehicleTypeService $vehicle_type_service;

    public function __construct(VehicleTypeService $vehicle_type_service)
    {
        $this->vehicle_type_service = $vehicle_type_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $vehicles_types = $this->vehicle_type_service->getList($input);
        return $this->successResponse('', $vehicles_types);
    }

    /**
     * Store a newly created resource in storage
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'vehicle_category_id' => 'required',
            'vehicle_company_id' => 'required'
        ],[
            'vehicle_category_id.required' => 'The category field is required.',
            'vehicle_company_id.required' => 'The company field is required.'
        ]);
        $input = $request->only(['title', 'description','vehicle_category_id','vehicle_company_id']);
        $vehicle_type = $this->vehicle_type_service->createItem($input);
        return $this->successResponse('Vehicle type added successfully!', $vehicle_type);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $vehicle_type = $this->vehicle_type_service->getItemDetails($id);
        return $this->successResponse('', $vehicle_type);
    }

    /**
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $this->vehicle_type_service->updateItem($id, $input);
        return $this->successResponse('Vehicle details updated successfully!');
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->vehicle_type_service->deleteItem($id);
        return $this->successResponse('vehicle type deleted successfully!');
    }

    /**
     * @return JsonResponse
     */
    public function getList()
    {
        $list = $this->vehicle_type_service->getListData();
        return $this->successResponse('', $list);
    }
}
