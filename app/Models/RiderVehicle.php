<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class RiderVehicle extends Model
{
    use HasFactory,SoftDeletes;
    protected $appends = ['is_pending'];
    protected $guarded = ['id', '_token'];
    public function vehicleType()
    {
        return $this->belongsTo('App\Models\VehicleType');
    }

    public function getStatusAttribute($value)
    {
        if (request()->is('api/admin/rider/rider-details')){
            return $value;
        }
        $booking_operations = config('global.operations');
        return $booking_operations[$value];
    }

    public function getIsPendingAttribute()
    {
        if($this->attributes['status'] === "pending" or $this->attributes['status'] === "cancelled" or $this->attributes['status'] === "in_progress") {
            return true;
        }
        return false;
    }
}
