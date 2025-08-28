<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billprintthermallog extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';

    protected $fillable = [
        'propertyid',
        'docid',
        'billdata',
        'printerpath',
        'psno',
        'u_entdt',
        'u_updatedt',
        'u_name'
    ];
}
