<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMarketplaceMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $marketplace = $request->route('marketplace');

        // If marketplace is a string (slug), resolve the model
        if (is_string($marketplace)) {
            $marketplace = \App\Models\Marketplace::where('slug', $marketplace)->first();
        }

        if (! $marketplace) {
            abort(404, 'Marketplace not found.');
        }

        if (! $user || ! $marketplace->isMember($user)) {
            abort(403, 'You must be a member of this marketplace.');
        }

        return $next($request);
    }
}
