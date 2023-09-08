<?php

namespace App\Repositories;

use App\Interfaces\BookingStatusLogInterface;
use App\Models\BookingStatusLog;

class BookingStatusLogRepository extends BaseRepository implements BookingStatusLogInterface
{
    public function __construct(BookingStatusLog $model)
    {
        parent::__construct($model);
    }
}
