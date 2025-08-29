<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDiscount extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'companydiscount';

    protected $fillable = [
        'sn',
        'propertyid',
        'compcode',
        'sno',
        'roomcatcode',
        'adult',
        'fixrate',
        'plan',
        'planamount',
        'taxinc',
        'u_name',
        'u_entdt',
        'u_ae'
    ];
}
