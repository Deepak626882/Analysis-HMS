<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indent1 extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $primaryKey = 'sn';

    protected $table = 'indent1';

    protected $fillable = [
        'sn',
        'propertyid',
        'docid',
        'vtype',
        'vno',
        'vprefix',
        'vdate',
        'sno',
        'item',
        'qty',
        'unit ',
        'vqty',
        'balqty',
        'wtunit',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae'
    ];
}
