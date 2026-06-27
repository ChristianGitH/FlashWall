<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SubscriptionService;

class CheckWallCreationLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!SubscriptionService::canCreateWall($user)) {
            return redirect()->back()->with('error', SubscriptionService::getSubscriptionErrorMessage($user));
        }

        return $next($request);
    }
}
