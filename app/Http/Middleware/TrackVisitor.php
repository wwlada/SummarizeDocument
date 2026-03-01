<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    const COOKIE_NAME = 'visitor_token';
    const COOKIE_DAYS = 365;

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie(self::COOKIE_NAME) ?? Str::uuid()->toString();
        $now   = now();

        $visitor = User::firstOrNew(['visitor_token' => $token]);

        if ($visitor->exists) {
            $visitor->visits_count++;
            $visitor->ip_address      = $request->ip();
            $visitor->user_agent      = $request->userAgent();
            $visitor->last_visited_at = $now;
            $visitor->save();
        } else {
            $visitor->ip_address       = $request->ip();
            $visitor->user_agent       = $request->userAgent();
            $visitor->visits_count    = 1;
            $visitor->last_visited_at = $now;
            $visitor->save();
        }

        Auth::login($visitor);

        $response = $next($request);

        if (! $request->cookie(self::COOKIE_NAME)) {
            $response->cookie(self::COOKIE_NAME, $token, 60 * 24 * self::COOKIE_DAYS, '/', null, true, true);
        }

        return $response;
    }
}
