<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExceptionLog extends Model
{
    protected $connection = 'audit_logs';

    protected $table = 'exceptions';

    protected $fillable = [
        'user_id',
        'activity',
        'action',
        'date_time',
        'error_msg',
        'err_detail',
    ];
}

