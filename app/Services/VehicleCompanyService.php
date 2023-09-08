<?php

namespace App\Services;

use App\Interfaces\VehicleCompanyInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class VehicleCompanyService extends BaseService
{
    private VehicleCompanyInterface $vehicle_company_repository;
    public function __construct(VehicleCompanyInterface $vehicle_company_repository)
    {
        $this->vehicle_company_repository = $vehicle_company_repository;
    }

    /**
     * @param $request_data
     * @return array
     */
    public function getCompaniesList($request_data = [])
    {
        $search_params = [];
        $query_search_params = $request_data['search_query'] ?? [];
        if (!empty($query_search_params)) {
            $query_search_params = [
                ["key" => 'title', "value" => $query_search_params]
            ];
        }
        $order_by_params = [
            'id' => 'DESC'
        ];
        $selected_columns = ['id','title','created_at'];
        $relations = [];
        $limit = $request_data['limit'] ?? 50;
        $data = [];
        try {
            $data = $this->vehicle_company_repository->getData(
                $search_params,
                $query_search_params,
                $selected_columns,
                $relations,
                $order_by_params,
                $limit
            );
        }catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
        return [
            'items' => $data->getCollection(),
            'pagination' => paginationData($data)
        ];
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public function getItem($id)
    {
        $company = null;
        try {
            $company = $this->vehicle_company_repository->getData(
                [
                    ['id','=',$id]
                ]
            )->firstOrFail();
        }catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
        return $company;
    }

    /**
     * @param array $request_data
     * @return Model|void
     */
    public function addCompany(array $request_data)
    {
        try {
            return $this->vehicle_company_repository->create([
                'title' => $request_data['title']
            ]);
        }catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @param array $request_data
     * @return bool|void
     */
    public function updateCompany($id,array $request_data = [])
    {
        try {
            return $this->vehicle_company_repository->update($id,$request_data);
        }catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @return bool|void
     */
    public function deleteItem($id)
    {
        try {
            return $this->vehicle_company_repository->delete($id);
        }catch (\Exception $exception) {
            $message = $exception->getMessage();
            if (empty($exception->getMessage())){
                $message = $exception->getResponse()->getData()->message;
            }
            $this->errorResponse($message);
        }
    }

    public function getList()
    {
        return $this->vehicle_company_repository->all(
            [],
            [],
            ['id' => 'DESC'],
        );
    }
}
