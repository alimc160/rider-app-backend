<?php

namespace App\Repositories;

use App\Interfaces\RoleInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepository implements RoleInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}
