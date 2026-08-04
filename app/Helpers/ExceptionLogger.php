<?php

namespace App\Helpers;

use App\Models\ExceptionLog;

class ExceptionLogger
{
    public static function log($e, $activity = 'Unknown', $action = 'N/A', $userId = null, $request = null)
    {
        // ExceptionLog::create([
        //     'user_id'    => $request->user()->id ?? $userId,
        //     'activity'   => $activity,
        //     'action'     => $action,
        //     'date_time'  => now(),
        //     'error_msg'  => $e->getMessage(),
        //     'err_detail' => $e->getTraceAsString(),
        // ]);
    }
}
