<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomMast extends Model
{
    use HasFactory;
    protected $primaryKey = 'sno';
    public $incrementing = false;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'room_mast';
}
