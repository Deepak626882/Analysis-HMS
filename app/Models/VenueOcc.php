<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueOcc extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'venueocc';

    protected $fillable = [
        'propertyid',
        'fpdocid',
        'venucode',
        'sno',
        'fromdate',
        'dromtime',
        'todate',
        'totime',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae'
    ];
}
