<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Domain
{
    /**
     * Origins allowed to call the API in production.
     *
     * @var list<string>
     */
    protected array $hosts = [
        'deltavcreative.com',
        'localhost',
        '127.0.0.1',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! App::environment('production')) {
            return $next($request);
        }

        $origin = parse_url((string) $request->headers->get('origin'), PHP_URL_HOST);

        if (in_array($origin, $this->hosts, true)) {
            Log::info("Next request from origin: {$origin}");

            return $next($request);
        }

        Log::info("Request blocked from the origin: {$origin}");

        return response('Request is not from the correct domain.', 403);
    }
}
