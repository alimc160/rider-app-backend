<?php

namespace App\Repositories;

use App\Interfaces\BookingOrderInterface;
use App\Interfaces\BookingOrderPackageInterface;
use App\Models\BookingOrderPackage;

class BookingOrderPackageRepository extends BaseRepository implements BookingOrderPackageInterface
{
    public function __construct(BookingOrderPackage $model)
    {
        parent::__construct($model);
    }
}
