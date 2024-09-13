<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the user is authenticated and whether their email is verified.
     * If the user is not authenticated, it clears the session and redirects them to the homepage.
     * If the user is authenticated, it stores their email verification status in the session.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request instance.
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next  The next middleware or request handler.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse  The response returned by the next middleware or redirect if unauthenticated.
     */
    public function handle(Request $request, Closure $next)
    {
        /* Check if the user is not authenticated */
        if (!Auth::check()) {
            /* If the user is not authenticated, flush (clear) the entire session. */
            session()->flush();
            /* Redirect the user to the homepage */
            return redirect()->route('login')->withErrors(['error' => 'Please log in to access this page.']);
        }

        /* Store the user's email verification status in the session */
        session(['is_verified', !empty(auth()->user()->email_verified_at)]);

        /* Continue processing the request */
        return $next($request);
    }
}
