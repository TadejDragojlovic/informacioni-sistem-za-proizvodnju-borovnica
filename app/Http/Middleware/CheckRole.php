<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $allowedRoles = array_filter(
            array_map(fn (string $role) => UserRole::tryFrom($role)?->value, $roles)
        );

        if (! $request->user() || ! in_array($request->user()->role, $allowedRoles, true)) {
            return redirect('/')->with('error', 'Nemate pristup ovoj stranici.');
        }

        return $next($request);
    }
}
