<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookinPlanDetail extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $primaryKey = 'sn';
    protected $table = 'bookingplandetails';

    protected $fillable = [
        'sn',
        'propertyid',
        'foliono',
        'docid',
        'sno',
        'sno1',
        'roomno',
        'rev_code',
        'taxinc',
        'taxstru',
        'fixrate',
        'noofdays',
        'planper',
        'amount',
        'room_rate_before_tax',
        'total_rate',
        'pcode',
        'netplanamt',
        'u_name',
        'u_entdt',
        'u_updatedt',
        'u_ae',
    ];
}
