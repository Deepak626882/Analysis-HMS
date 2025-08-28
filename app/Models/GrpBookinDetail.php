<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrpBookinDetail extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $primaryKey = 'sn';
    protected $table = 'grpbookingdetails';

    protected $fillable = [
        'sn',
        'Property_ID',
        'BookingDocid',
        'BookNo',
        'GuestName',
        'GuestProf',
        'Sno',
        'RoomDet',
        'Adults',
        'Childs',
        'Tarrif',
        'IncTax',
        'U_EntDt',
        'u_updatedt',
        'U_AE',
        'RoomCat',
        'ccode',
        'pcode',
        'Plan_Code',
        'Remarks',
        'ServiceChrg',
        'RoomNo',
        'RateCode',
        'ArrDate',
        'ArrTime',
        'NoDays',
        'DepDate',
        'Cancel',
        'CancelDate',
        'U_Name',
        'DepTime',
        'status',
        'RoomTaxStru',
        'CancelUName',
        'extraadd',
        'ContraDocId',
        'ContraSno',
        'chkoutyn'
    ];
}
