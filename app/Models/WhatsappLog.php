<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'propertyid',
        'recipient_phone_number',
        'type',
        'template_id',
        'parameters',
        'response',
        'http_code',
        'status',
        'u_name'
    ];
}
