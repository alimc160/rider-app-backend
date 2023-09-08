<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id', '_token'];

    /**
     * @return HasMany
     */
    public function vehicleTypes(): HasMany
    {
        return $this->hasMany('App\Models\VehicleType');
    }
}
