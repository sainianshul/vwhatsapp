<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Only allow Admin and Manager roles through.
     * Regular users get a 403.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role === \App\Models\User::ROLE_USER) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
