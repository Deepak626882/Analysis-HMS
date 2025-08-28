<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRate extends Model
{
    use HasFactory;
    const CREATED_AT = 'U_EntDt';
    const UPDATED_AT = 'U_updatedt';
    protected $table = 'itemrate';
}
