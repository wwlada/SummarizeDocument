<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SummaryAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('Unauthenticated request reached SummaryAccess');
            return response()->json(['message' => 'Something went wrong. Please refresh the page and try again.'], 401);
        }

        $limit = config('ai.providers.openai.max_tokens_spend_per_day_per_user');
        $todayTotalTokensSpendByAuth = $user
            ->analytics()
            ->whereDate('created_at', now()->toDateString())
            ->sum('total_tokens');

        $todayTotalTokensSpendByDevice = User::where('ip_address', $user->ip_address)
            ->where('user_agent', $user->user_agent)
            ->first()
            ?->analytics()
            ?->whereDate('created_at', now()->toDateString())
            ?->sum('total_tokens');

        if ($todayTotalTokensSpendByAuth > $limit
            || ($todayTotalTokensSpendByDevice ?? 0) > $limit) {
            Log::warning("User $user->id exceeded daily token limit: authUsed=$todayTotalTokensSpendByAuth, deviceUsed=".($todayTotalTokensSpendByDevice ?? 0));
            return response()->json(['message' => 'You have reached your daily usage limit. Please try again tomorrow.'], 429);
        }

        return $next($request);
    }
}
