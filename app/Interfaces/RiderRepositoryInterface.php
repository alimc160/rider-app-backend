<?php

namespace App\Interfaces;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RiderRepositoryInterface
{
    public function register(array $attributes): Model;
    public function updateRider(object $rider,array $attributes): bool;
    public function addRiderLocationLogs(array $attributes): Model;
    public function getCities(): Collection;
    public function updateOrCreateMedia(array $exiting_attributes, array $update_attributes): Model;
    public function updateRiderLicence(int $rider_id,array $attributes): Model;
    public function updateSelfiePictureStatus(int $rider_id,array $attributes): Model;
    public function updateCnicStatus(int $rider_id,array $attributes): Model;
    public function updateContractStatus(int $rider_id,array $attributes): Model;
    public function getLicenceStatus(int $rider_id,string $status): ?Model;
    public function getSelfiePictureStatus(int $rider_id,string $status): ?Model;
    public function getCnicStatus(int $rider_id,string $status): ?Model;
    public function getContractStatus(int $rider_id,string $status,int $id): ?Model;

    public function all(array $where_params = [], array $where_in_params = [], array $order_by_params = [], array $relations = [], array $select_columns = []): Collection;
}
