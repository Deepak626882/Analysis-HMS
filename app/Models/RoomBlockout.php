<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomBlockout extends Model
{
    use HasFactory;
    protected $primaryKey = 'sn';
    public $incrementing = false;
    protected $table = 'roomblockout';
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
}
