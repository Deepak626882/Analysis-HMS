<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suntranlog extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'suntranlog';
    public $incrementing = true;

    protected $fillable = [
        'propertyid',
        'docid',
        'sno',
        'vtype',
        'vno',
        'vdate',
        'partycode',
        'suncode',
        'dispname',
        'calcformula',
        'svalue',
        'amount',
        'baseamount',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae',
        'sunappdate',
        'revcode',
        'restcode',
        'delflag'
    ];
}
