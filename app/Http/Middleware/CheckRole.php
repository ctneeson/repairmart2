<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Routes that should bypass strict role checking
     * and defer to controller logic instead
     */
    protected $bypassRoutes = [
        'listings.create',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // If current route is in the bypass list, skip role checking
        $routeName = $request->route()->getName();
        if (in_array($routeName, $this->bypassRoutes)) {
            return $next($request);
        }

        // Otherwise proceed with normal role checking
        if (!$request->user()->hasRole($roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}