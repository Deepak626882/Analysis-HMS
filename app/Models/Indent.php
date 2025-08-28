<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indent extends Model
{
    use HasFactory;

    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $primaryKey = 'sn';

    protected $table = 'indent';

    protected $fillable = [
        'sn',
        'propertyid',
        'docid',
        'vtype',
        'vno',
        'vprefix',
        'vdate',
        'vtime',
        'department',
        'godown',
        'remarks',
        'veryfiuser',
        'veryfidate',
        'veryfiremark',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae',
        'company',
        'refdocId',
    ];
}
