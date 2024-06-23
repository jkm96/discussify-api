<?php

namespace App\Http\Middleware;

use App\Utils\Helpers\ResponseHelpers;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::guard('admin')->user()) {
            return $next($request);
        }

        return ResponseHelpers::ConvertToJsonResponseWrapper(
            ['error' => 'You do not have access'],
            'Error: internal server error',
             500
        );
    }
}
