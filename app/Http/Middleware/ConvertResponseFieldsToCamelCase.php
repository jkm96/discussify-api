<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ConvertResponseFieldsToCamelCase
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $content = $response->getContent();

        try {
            $json = json_decode($content, true);
            $replaced = $this->convertKeysToCamelCase($json);
            $response->setContent(json_encode($replaced));
        } catch (\Exception $e) {
            // you can log an error here if you want
        }

        return $response;
    }

    /**
     * Recursively convert keys to camel case.
     *
     * @param array $array
     * @return array
     */
    private function convertKeysToCamelCase(array $array): array
    {
        $replaced = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // If the value is an array, recursively convert its keys
                $replaced[Str::camel($key)] = $this->convertKeysToCamelCase($value);
            } else {
                $replaced[Str::camel($key)] = $value;
            }
        }
        return $replaced;
    }
}
