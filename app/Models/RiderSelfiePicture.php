<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiderSelfiePicture extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id','_token'];
//    protected $appends = ['is_pending'];
//
//    public function getIsPendingAttribute()
//    {
//        if ($this->attributes['status'] !== "approved") {
//            return true;
//        }
//        return false;
//    }
}
