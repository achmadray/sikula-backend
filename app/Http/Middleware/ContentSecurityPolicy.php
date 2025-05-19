<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $csp = "script-src 'self' 'unsafe-eval' https://d2f3dnusg0rbp7.cloudfront.net https://api.sandbox.midtrans.com https://pay.google.com https://js-agent.newrelic.com https://bam.nr-data.net'";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
