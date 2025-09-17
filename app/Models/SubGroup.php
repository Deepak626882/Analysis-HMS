<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubGroup extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'subgroup';

    protected $fillable = [
        'sub_code',
        'propertyid',
        'name',
        'group_code',
        'nature',
        'comp_type',
        'allow_credit',
        'conprefix',
        'conperson',
        'address',
        'citycode',
        'pin',
        'mobile',
        'email',
        'panno',
        'tds_catg',
        'activeyn',
        'allowcredit',
        'creditlimit',
        'creditdays',
        'remark',
        'discounttype',
        'discount',
        'religion',
        'remarks',
        'blacklisted',
        'reasonblacklist',
        'blacklistedby',
        'sysYN',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae',
        'gstin',
        'mapcode',
        'dealertype',
        'legalname',
        'tradename',
        'subyn',
        'membership_date',
        'member_id',
        'addtype'
    ];
}
