<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleType extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id', '_token'];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo('App\Models\VehicleCategory','vehicle_category_id');
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo('App\Models\VehicleCompany','vehicle_company_id');
    }

    /**
     * @return HasMany
     */
    public function riderVehicles(): HasMany
    {
        return $this->hasMany('App\Models\RiderVehicle');
    }
}
