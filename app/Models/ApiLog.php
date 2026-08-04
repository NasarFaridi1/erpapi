<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
     protected $connection = 'audit_logs'; 
    protected $table = 'api_logs';
    protected $fillable = [
        'method', 'endpoint', 'request_payload', 'response_payload',
        'status_code', 'ip_address', 'user_agent',
        'exception_message', 'exception_trace'
    ];
}
