<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiderMedia extends Model
{
    use HasFactory,SoftDeletes;

    protected $appends = ['is_pending'];

    protected $guarded = ['id','_token'];

    public function getStatusAttribute($value)
    {
        $booking_operations = config('global.operations');
        return $booking_operations[$value];
    }
    public function getIsPendingAttribute($value)
    {
        if($this->attributes['status'] === "pending" or $this->attributes['status'] === "cancelled") {
            return true;
        }
        return false;
    }
}
