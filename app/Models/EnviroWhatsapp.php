<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnviroWhatsapp extends Model
{
    use HasFactory;

    
    protected $table = 'enviro_whatsapp';
    protected $primaryKey = 'propertyid';
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
}
