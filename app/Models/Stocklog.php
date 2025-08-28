<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stocklog extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'stocklog';
    public $incrementing = true;

    protected $fillable = [
        'propertyid', 'docid', 'sno', 'vtype', 'vno', 'vprefix', 'vdate', 'partycode', 'restcode',
        'roomcat', 'roomtype', 'roomno', 'contradocid', 'contrasno', 'item', 'qtyiss', 'qtyrec',
        'unit', 'rate', 'amount', 'taxper', 'taxamt', 'discper', 'discamt', 'description', 'voidyn',
        'remarks', 'kotdocid', 'kotsno', 'vtime', 'u_name', 'u_entdt', 'u_updatedt', 'u_ae', 'total',
        'discapp', 'roundoff', 'departcode', 'godowncode', 'chalqty', 'recdqty', 'accqty', 'rejqty',
        'recdunit', 'specification', 'itemrate', 'delflag', 'landval', 'convratio', 'indentdocid',
        'indentsno', 'issqty', 'issueunit', 'freesno', 'schemecode', 'seqno', 'company', 'itemrestcode',
        'schrgapp', 'schrgper', 'schrgamt', 'refdocid'
    ];
}
