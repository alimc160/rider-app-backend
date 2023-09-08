<?php

namespace App\Repositories;

use App\Interfaces\VehicleCompanyInterface;
use App\Models\VehicleCompany;

class VehicleCompanyRepository extends BaseRepository implements VehicleCompanyInterface
{
    public function __construct(VehicleCompany $model)
    {
        parent::__construct($model);
    }
}
