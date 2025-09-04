<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuHelp extends Model
{
    use HasFactory;
    
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'menuhelp'; 

    protected $fillable = [
        'propertyid',
        'username',
        'compcode',
        'opt1',
        'opt2',
        'opt3',
        'code',
        'route',
        'module',
        'module_name',
        'view',
        'ins',
        'edit',
        'del',
        'print',
        'flag',
        'outletcode',
        'u_entdt',
        'u_updatedt',
        'u_name',
    ];
}
