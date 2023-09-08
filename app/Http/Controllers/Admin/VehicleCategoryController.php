<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VehicleCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleCategoryController extends Controller
{
    private VehicleCategoryService $vehicle_category_service;
    public function __construct(VehicleCategoryService $vehicle_category_service)
    {
        $this->vehicle_category_service = $vehicle_category_service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $vehicle_categories = $this->vehicle_category_service->getList($input);
        return $this->successResponse('',$vehicle_categories);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:250'
        ]);
        $input = $request->only(['title']);
        $vehicle_category=$this->vehicle_category_service->addItem($input);
        return $this->successResponse('Category added successfully.',$vehicle_category);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $details = $this->vehicle_category_service->getDetails($id);
        return $this->successResponse('',$details);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update($id,Request $request)
    {
        $input = $request->only(['title']);
        $this->vehicle_category_service->updateItem($input,$id);
        return $this->successResponse('Category updated successfully.');
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->vehicle_category_service->deleteItem($id);
        return $this->successResponse('Category deleted successfully!');
    }

    /**
     * @return JsonResponse
     */
    public function getCategoriesList()
    {
        $data = $this->vehicle_category_service->getCategories();
        return $this->successResponse('',$data);
    }
}
