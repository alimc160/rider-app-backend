<?php

namespace App\Repositories;

use App\Models\User;

class AdminRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
