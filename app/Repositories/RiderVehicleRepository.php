<?php

namespace App\Repositories;

use App\Interfaces\RiderVehicleInterface;
use App\Models\RiderVehicle;
use Illuminate\Database\Eloquent\Model;

class RiderVehicleRepository extends BaseRepository implements RiderVehicleInterface
{
    public function __construct(RiderVehicle $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $exiting_attributes
     * @param array $update_attributes
     * @return Model
     */
    public function updateOrCreate(array $exiting_attributes, array $update_attributes): Model
    {
        return $this->model->updateOrCreate($exiting_attributes,$update_attributes);
    }
}
