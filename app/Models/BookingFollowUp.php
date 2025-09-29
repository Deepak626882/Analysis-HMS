<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingFollowUp extends Model
{
    use HasFactory;

     protected $fillable = [
        'id',
        'inqno',
        'propertyid',
        'nextfollowupdate',
        'remark',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae',
    ];

}
