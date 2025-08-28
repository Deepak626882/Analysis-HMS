<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;
    const CREATED_AT = 'u_entdt';
    const UPDATED_AT = 'u_updatedt';
    protected $table = 'ledger';
    protected $primaryKey = 'docid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'delflag',
    ];
}
