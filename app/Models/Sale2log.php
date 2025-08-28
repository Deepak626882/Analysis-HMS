<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale2log extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'sale2log';
    public $incrementing = true;

    protected $fillable = [
        'propertyid',
        'docid',
        'sno',
        'sno1',
        'vtype',
        'vno',
        'vprefix',
        'vtime',
        'vdate',
        'restcode',
        'taxcode',
        'basevalue',
        'taxper',
        'taxamt',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae',
        'delflag'
    ];
}
