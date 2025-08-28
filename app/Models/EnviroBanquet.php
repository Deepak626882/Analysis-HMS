<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnviroBanquet extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'enviro_banquet';

    protected $primaryKey = 'propertyid';
    
    protected $fillable = [
        'propertyid',
        'outdoorcatering',
        'cataloglimit',
        'roundoffac',
        'discountac',
        'indoorsaleac',
        'indoorpartyac',
        'u_name',
        'u_ae',
        'u_entdt',
        'u_updatedt'
    ];
}
