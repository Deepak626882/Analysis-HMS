<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale1log extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'sale1log';
    public $incrementing = true;

    protected $fillable = [
        'propertyid',
        'docid',
        'vtype',
        'vno',
        'vtime',
        'vprefix',
        'vdate',
        'restcode',
        'roomcat',
        'roomtype',
        'roomno',
        'foliono',
        'sno1',
        'party',
        'total',
        'discper',
        'discamt',
        'nontaxable',
        'taxable',
        'servicecharge',
        'addamt',
        'dedamt',
        'roundoff',
        'netamt',
        'remark',
        'waiter',
        'kotno',
        'tokenno',
        'guaratt',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae',
        'delflag',
        'printed',
        'deliveredyn',
        'custname',
        'phoneno',
        'add',
        'city',
        'cashrecd',
        'folionodocid',
        'au_name',
        'au_entdt',
        'discremark',
        'cgst',
        'sgst',
        'igst'
    ];
}
