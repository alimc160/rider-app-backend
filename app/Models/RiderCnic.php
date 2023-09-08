<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiderCnic extends Model
{
    use HasFactory,SoftDeletes;

//    protected $appends = ['is_pending'];
    protected $guarded = ['id','_token'];
//    public function getIsPendingAttribute()
//    {
//        if ($this->attributes['status'] !== "approved") {
//            return true;
//        }
//        return false;
//    }
}
