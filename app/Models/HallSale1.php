<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallSale1 extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'hallsale1';

    protected $fillable = [
        'sn',
        'propertyid',
        'docId',
        'vtype',
        'vno',
        'vdate',
        'restcode',
        'party',
        'total',
        'discper',
        'discamt',
        'nontaxable',
        'taxable',
        'netamt',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae',
        'remark',
        'noofpax',
        'rateperpax',
        'totalpercover',
        'advance',
        'rectno',
        'rectdate',
        'bookdocid',
        'narration',
        'narration1',
        'cgst',
        'sgst',
    ];
}
