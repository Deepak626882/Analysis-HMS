<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnviroGeneral extends Model
{
    use HasFactory;
    protected $primaryKey = 'propertyid';
    public $incrementing = false;
    protected $keyType = 'string';  
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'enviro_general';
}
