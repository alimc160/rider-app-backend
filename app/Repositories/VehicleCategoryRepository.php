<?php

namespace App\Repositories;


use App\Interfaces\VehicleCategoryInterface;
use App\Models\VehicleCategory;
use Illuminate\Database\Eloquent\Collection;

class VehicleCategoryRepository extends BaseRepository implements VehicleCategoryInterface
{
    public function __construct(VehicleCategory $model)
    {
        parent::__construct($model);
    }
}
