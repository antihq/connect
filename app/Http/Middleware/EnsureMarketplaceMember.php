<?php

namespace App\Http\Middleware;

use App\Models\Marketplace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** @package App\Http\Middleware */
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

        if (is_string($marketplace)) {
            $marketplace = Marketplace::where('slug', $marketplace)->first();
        }

        if (! $marketplace) {
            abort(404, 'Marketplace not found.');
        }

        if (! $user || ! $marketplace->isMember($user)) {
            return redirect()->route('on-marketplace.sign-in', $marketplace);
        }

        return $next($request);
    }
}
