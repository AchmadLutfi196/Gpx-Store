<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminPanelAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Always check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        // Get current path to determine what page we're on
        $path = $request->path();
        
        // Allow access to user profile and user-specific pages for all authenticated users
        if (str_contains($path, 'users') || str_contains($path, 'profile') || str_contains($path, 'account')) {
            return $next($request);
        }

        // For all other admin pages, check for admin role
        if (Auth::user()->role !== 'admin') {
            // Store intended URL for redirection after login
            session()->put('url.intended', url()->current());
            
            // Redirect to login with message
            Auth::logout();
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Restricted area. Please log in with an admin account.');
        }

        return $next($request);
    }
}