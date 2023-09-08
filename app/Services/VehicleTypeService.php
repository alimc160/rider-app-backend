<?php

namespace App\Services;

use App\Interfaces\VehicleTypeInterface;

class VehicleTypeService extends BaseService
{
    private VehicleTypeInterface $vehicle_type_repository;

    public function __construct(VehicleTypeInterface $vehicle_type_repository)
    {
        $this->vehicle_type_repository = $vehicle_type_repository;
    }

    public function getList($request_data = [])
    {
        $vehicle_type_repository = $this->vehicle_type_repository;
        $search_params = $request_data['search_params'] ?? [];
        $query_search_params = $request_data['search_query'] ?? [];
        if (!empty($query_search_params)) {
            $query_search_params = [
                ["key" => 'title', "value" =>$query_search_params],
                ["key" => 'description', "value" =>$query_search_params]
            ];
        }
        $select_columns = [];
        $order_by_params = [
            'id' => 'DESC'
        ];
        $limit = $request_data['limit'] ?? 50;
        $data = null;
        try {
            $data = $vehicle_type_repository->getData(
                $search_params,
                $query_search_params,
                $select_columns,
                [
                    'category:id,title',
                    'company:id,title'
                ],
                $order_by_params,
                $limit,
            );
        } catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
        return [
            "items" => $data->getCollection(),
            "pagination" => paginationData($data)
        ];
    }

    public function createItem($request_data)
    {
        try {
            return $this->vehicle_type_repository->create([
                'title' => $request_data['title'],
                'description' => $request_data['description'],
                'vehicle_category_id' => $request_data['vehicle_category_id'],
                'vehicle_company_id' => $request_data['vehicle_company_id']
            ]);
        } catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
    }

    public function getItemDetails($id,$request = [])
    {
        try {
            return $this->vehicle_type_repository->getData(
                [
                    ['id', '=', $id]
                ]
            )->firstOrFail();
        } catch (\Exception $exception) {
            $this->errorResponse($exception->getMessage());
        }
    }

    public function updateItem($id, $request_data)
    {
        try {
            return $this->vehicle_type_repository->update($id, $request_data);
        } catch (\Exception $exception) {
            $this->errorResponse($exception->getMessage());
        }
    }

    public function deleteItem($id)
    {
        try {
            return $this->vehicle_type_repository->delete($id);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            if (empty($exception->getMessage())){
                $message = $exception->getResponse()->getData()->message;
            }
            $this->errorResponse($message);
        }
    }

    public function getListData()
    {
        try {
            return $this->vehicle_type_repository->all(
                [],
                [],
                ['id' => 'DESC'],
                [],
                ['id','title','description'],
            );
        } catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
    }
}
