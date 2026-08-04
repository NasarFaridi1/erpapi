<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Company;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActivePackage
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        // Fetch company for this user
        $company = Company::where('user_id', $user->id)->first();

        if (!$company) {
            return response()->json([
                'message' => 'Failed to fetch active package',
                'error'   => 'Company profile not found',
            ], 404);
        }

        // check if package is active
        if (!$company->package_active) {
            return response()->json([
                'message' => 'Your package is not active',
            ], 403);
        }

        return $next($request);
    }
}
