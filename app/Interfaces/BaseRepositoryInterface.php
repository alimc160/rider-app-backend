<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function register(array $attributes): Model;
    public function find(int $id): ?Model;
    public function update(int $id, array $attributes): bool;
    public function delete(int $id): bool;
    public function getData(array $search_params,array $search_query_params ,array $selected_columns,array $relations ,array $order_by_params, int $limit, array $or_where_search_params, array $between_filters, array $group_by_columns, array $having_params): LengthAwarePaginator;
//    public function updateOrCreateDetails(array $exiting_attributes,array $update_attributes): Model;
    public function create(array $attributes): Model;
    public function all(array $where_params,array $where_in_params,array $order_by_params, array $relations ,array $select_columns): Collection;
}
