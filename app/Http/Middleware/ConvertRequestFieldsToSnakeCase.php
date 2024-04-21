<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConvertRequestFieldsToSnakeCase
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
        $requestData = $request->all();
        $convertedData = $this->convertKeysToSnakeCase($requestData);
        $request->replace($convertedData);

        return $next($request);
    }

    /**
     * Recursively convert keys to snake case.
     *
     * @param array $array
     * @return array
     */
    private function convertKeysToSnakeCase(array $array): array
    {
        $converted = [];
        foreach ($array as $key => $value) {
            $converted[Str::snake($key)] = is_array($value) ? $this->convertKeysToSnakeCase($value) : $value;
        }
        return $converted;
    }
}
