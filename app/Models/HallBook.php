<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallBook extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'hallbook';


    protected $fillable = [
        'propertyid',
        'docid',
        'vtype',
        'vno',
        'vtime',
        'vprefix',
        'vdate',
        'partyname',
        'add1',
        'add2',
        'city',
        'panno',
        'mobileno',
        'mobileno1',
        'func_name',
        'restcode',
        'housekeeping',
        'frontoff',
        'engg',
        'security',
        'chef',
        'board',
        'menuspl1',
        'menuspl2',
        'menuspl3',
        'menuspl4',
        'menuspl5',
        'menuspl6',
        'menuspl7',
        'expatt',
        'guaratt',
        'coverrate',
        'companycode',
        'remark',
        'bookingagent',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae'
    ];   
}
