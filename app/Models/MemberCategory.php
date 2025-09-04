<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberCategory extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'member_categories';

    protected $fillable = [
        'sn',
        'propertyid',
        'code',
        'title',
        'short_name',
        'subscription',
        'surcharge',
        'facility_billing',
        'status',
    ];
}
