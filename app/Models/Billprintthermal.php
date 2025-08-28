<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billprintthermal extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';

    protected $fillable = [
        'docid',
        'propertyid',
        'billdata',
        'printerpath',
        'psno',
        'u_entdt',
        'u_updatedt',
        'u_name'
    ];
}
