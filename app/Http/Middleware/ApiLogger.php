<?php
// app/Http/Middleware/ApiLogger.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Auth;

class ApiLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            ApiLog::create([
                'user_id' => Auth::id() ? $request->user()->id : null,
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'request_payload' => json_encode($request->all()),
                'response_payload' => method_exists($response, 'getContent') ? $response->getContent() : null,
                'status_code' => $response->getStatusCode(),
                'ip_address' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            
        }

        return $response;
    }
}
