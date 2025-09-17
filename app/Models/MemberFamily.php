<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberFamily extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'memberfamily';

    protected $fillable = [
        'propertyid',
        'subcode',
        'sno',
        'relationship',
        'conprefix',
        'name',
        'gender',
        'maritalstatus',
        'dob',
        'pob',
        'weddate',
        'mobile',
        'email',
        'nationality',
        'religion',
        'bloodgroup',
        'proocc',
        'edquali',
        'tbusiness',
        'turnover',
        'desig',
        'passport',
        'pan',
        'intax',
        'spinterest',
        'picpath',
        'signpath',
        'label',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae',
        'hw_id',
        'cardno',
        'cardissdate',
        'cardregid',
        'cardvalidupto',
        'postedat',
        'mobile1'
    ];
}
