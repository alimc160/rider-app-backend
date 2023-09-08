<?php

namespace App\Services;

use App\Interfaces\VehicleCategoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class VehicleCategoryService extends BaseService
{
    private VehicleCategoryInterface $vehicle_category_repository;

    public function __construct(VehicleCategoryInterface $vehicle_category_repository)
    {
        $this->vehicle_category_repository = $vehicle_category_repository;
    }

    public function getList($request_data)
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
        $selected_columns = [];
        $relations = [];
        $limit = $request_data['limit'] ?? 50;
        $data = [];
        try {
            $data = $this->vehicle_category_repository->getData(
                $search_params,
                $query_search_params,
                $selected_columns,
                $relations,
                $order_by_params,
                $limit
            );
        } catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
        return [
            'items' => $data->getCollection(),
            'pagination' => paginationData($data)
        ];
    }

    /**
     * @param $request_data
     * @return Model|void
     */
    public function addItem($request_data)
    {
        try {
            return $this->vehicle_category_repository->register(
                [
                    'title' => $request_data['title']
                ]
            );
        } catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function getDetails($id) {
        try {
            return $this->vehicle_category_repository->getData(
                [
                    ['id', '=', $id]
                ]
            )->firstOrFail();
        }catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $request_data
     * @param $id
     * @return bool|void
     */
    public function updateItem($request_data,$id)
    {
        try {
            return $this->vehicle_category_repository->update(
                $id,
                $request_data
            );
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
            return $this->vehicle_category_repository->delete($id);
        }catch (\Exception $exception) {
            $message = $exception->getMessage();
            if (empty($exception->getMessage())){
                $message = $exception->getResponse()->getData()->message;
            }
            $this->errorResponse($message);
        }
    }

    /**
     * @return Collection|void
     */
    public function getCategories()
    {
        try {
            return $this->vehicle_category_repository->all();
        }catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
    }
}
