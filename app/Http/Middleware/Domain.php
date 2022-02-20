<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class Domain
{
    protected $hosts = [
        'deltavcreative.com',
        'localhost',
        '127.0.0.1'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (App::environment('production')) {
            $hosts = collect($this->hosts);
            $origin = parse_url($request->headers->get('origin'),  PHP_URL_HOST);
            if ($hosts->contains($origin)) {
                Log::info("Next request from origin: {$origin}");
                return $next($request);
            } else {
                Log::info("Request blocked from the origin: {$origin}");
                return response('Request is not from the correct domain.', 403);
            }
        } else {
            return $next($request);
        }
    }
}
