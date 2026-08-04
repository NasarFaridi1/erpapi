<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Instead of redirecting to "login", return JSON error
            abort(response()->json([
                'message' => 'Unauthenticated',
                'error' => 'You must be logged in to access this resource.'
            ], 401));
        }
    }
}
