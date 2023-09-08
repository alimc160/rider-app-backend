<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Rider extends Authenticatable
{
	use HasFactory, SoftDeletes, HasApiTokens, HasRoles;
	protected $guarded = ['id'];
//    protected $appends = ['is_pending'];
    protected $guard_name = 'sanctum';
    protected $hidden = [
        'otp',
        'pivot'
    ];

    /**
     * Get the rider details associated with the rider.
     *
     * @return HasOne
     */
//    public function riderDetails(): HasMany
//    {
//        return $this->hasMany(RiderMedia::class,'rider_id');
//    }

    public function riderVehicle()
    {
        return $this->hasOne('App\Models\RiderVehicle');
    }

    public function riderSelfiePicture()
    {
        return $this->hasOne(RiderSelfiePicture::class,'rider_id');
    }

    public function riderCnic()
    {
        return $this->hasOne(RiderCnic::class,'rider_id');
    }

    public function riderContract()
    {
        return $this->hasOne(RiderContract::class,'rider_id');
    }

    public function riderLicence()
    {
        return $this->hasOne(RiderLicence::class,'rider_id');
    }

    public function getStatusAttribute($value)
    {
        $path = request()->path();
        if ($path === "api/admin/rider/rider-details" or $path === "api/admin/rider/list" or $path === "api/rider/update-active-status") {
            return $value;
        }
        $booking_operations = config('global.operations');
        return $booking_operations[$value];
    }

// apply global scope for all cancelled status riders
//    public function booted()
//    {
//        dd("ok");
//    }
//    public function getIsPendingAttribute()
//    {
//        if($this->attributes['status'] !== "approved") {
//            return true;
//        }
//        return false;
//    }
}
