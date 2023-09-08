<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VehicleCompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleCompanyController extends Controller
{
    private VehicleCompanyService $vehicle_company_service;
    public function __construct(VehicleCompanyService $vehicle_company_service)
    {
        $this->vehicle_company_service = $vehicle_company_service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $list = $this->vehicle_company_service->getCompaniesList($input);
        return $this->successResponse('',$list);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $vehicle_company = $this->vehicle_company_service->getItem($id);
        return $this->successResponse('',$vehicle_company);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:vehicle_companies'
        ]);
        $input = $request->only(['title']);
        $company = $this->vehicle_company_service->addCompany($input);
        return $this->successResponse('Vehicle company added successfully.',$company);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'title' => [
                'required',Rule::unique('vehicle_companies')->ignore($id)
            ]
        ]);
        $input = $request->only(['title']);
        $this->vehicle_company_service->updateCompany($id,$input);
        return $this->successResponse('Record updated successfully.');
    }

    public function destroy($id)
    {
        $this->vehicle_company_service->deleteItem($id);
        return $this->successResponse('Record deleted successfully.');
    }

    public function getCompaniesList()
    {
        $data = $this->vehicle_company_service->getList();
        return $this->successResponse('',$data);
    }
}
