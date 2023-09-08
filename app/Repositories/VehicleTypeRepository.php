<?php

namespace App\Repositories;

use App\Interfaces\VehicleTypeInterface;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class VehicleTypeRepository extends BaseRepository implements VehicleTypeInterface
{
    public function __construct(VehicleType $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }
}
