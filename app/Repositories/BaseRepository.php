<?php

namespace App\Repositories;


use App\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function register(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * @param $id
     * @return Model|null
     */
    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param $id
     * @param array $attributes
     * @return bool
     */
    public function update($id, array $attributes): bool
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    /**
     * @param array $search_params
     * @param array $search_query_params
     * @param array $selected_columns
     * @param array $relations
     * @param array $order_by_params
     * @param int $limit
     * @param array $or_where_search_params
     * @param array $between_filters
     * @param array $group_by_columns
     * @param array $having_params
     * @return LengthAwarePaginator
     */
    public function getData(
        array $search_params = [],
        array $search_query_params = [],
        array $selected_columns = [],
        array $relations = [],
        array $order_by_params = ['id' => 'DESC'],
        int   $limit = 50,
        array $or_where_search_params = [],
        array $between_filters = [],
        array $group_by_columns = [],
        array $having_params = []
    ): LengthAwarePaginator
    {
        $query = $this->model;
        if (count($search_params) > 0) {
            $query = $query->where($search_params);
        }
        if (count($search_query_params) > 0) {
            foreach ($search_query_params as $param) {
                $query = $query->orWhere(function ($q) use ($param) {
                    $q->orWhere($param['key'], 'LIKE', '%' . $param['value'] . '%');
                });
            }
        }
        foreach ($or_where_search_params as $param) {
            $query = $query->where(function ($q) use ($param) {
                $q->orWhere($param['column'], $param['operator'], $param['value']);
            });
        }
        foreach ($between_filters as $key => $filter) {
            $query = $query->whereBetween($key, $filter);
        }
        if (count($relations) > 0) {
            $query = $query->with($relations);
        }
        foreach ($group_by_columns as $by_column) {
            $query = $query->groupBy($by_column);
        }
        foreach ($order_by_params as $key => $item) {
            $query = $query->orderBy($key, $item);
        }
        foreach ($having_params as $having_param) {
            $query = $query->having(
                $having_param['column'],
                $having_param['operator'],
                $having_param['value']
            );
        }
        if (count($selected_columns) > 0) {
            $query = $query->addSelect($selected_columns);
        }
        return $query->paginate($limit);
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * @param array $where_params
     * @param array $where_in_params
     * @param array $order_by_params
     * @param array $relations
     * @param array $select_columns
     * @return Collection
     */
    public function all(
        array $where_params = [],
        array $where_in_params = [],
        array $order_by_params = [],
        array $relations = [],
        array $select_columns = []
    ): Collection
    {
        $query = $this->model;
        if (count($where_params)) {
            $query = $query->where($where_params);
        }
        if (count($where_in_params)) {
            $query = $query->whereIntegerInRaw($where_in_params['column'], $where_in_params['values']);
        }
        foreach ($order_by_params as $key => $by_param) {
            $query = $query->orderBy($key, $by_param);
        }
        if (count($relations) > 0) {
            $query = $query->with($relations);
        }
        if (count($select_columns) > 0) {
            $query = $query->addSelect($select_columns);
        }
        return $query->get();
    }
}
