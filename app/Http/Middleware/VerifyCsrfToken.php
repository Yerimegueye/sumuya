<?php

namespace App\Http\Middleware;
use Closure;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;
    public function handle($request, Closure $next)
                {
                   $response = $next($request);
                    $response->header('Content-Security-Policy', "script-src 'self' 'unsafe-eval'");
                    // ...
                    return $next($request);

                    return $response;
                }

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
