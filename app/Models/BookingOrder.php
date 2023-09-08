<?php

namespace App\Models;

use App\Http\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingOrder extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['_token'];
    protected $appends = ['distance','time_taken'];

    /**
     * @return HasMany
     */
    public function orderPackages(): HasMany
    {
        return $this->hasMany('App\Models\BookingOrderPackage');
    }

    /**
     * @return HasMany
     */
    public function bookingLogs(): HasMany
    {
        return $this->hasMany('App\Models\BookingStatusLog');
    }

    /**
     * @return float
     */
    public function getDistanceAttribute(): float
    {
        $distance=haversineGreatCircleDistance(
            $this->pickup_lat,
            $this->pickup_long,
            $this->drop_off_lat,
            $this->drop_off_long,
            6371
        );
        return round($distance,2);
    }

    public function getTimeTakenAttribute()
    {
        $logs = $this->bookingLogs()->where('booking_order_id',$this->id)->get();
        $first_record = $logs->first();
        $last_record = $logs->last();
        return $last_record->updated_at->diffInMinutes($first_record->updated_at);
    }
}
