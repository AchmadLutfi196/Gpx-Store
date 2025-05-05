<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class VerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Schema::hasColumn('users', 'email_verified_at')) {
            $user = Auth::user();
            
            if ($user->email_verified_at === null) {
                return redirect()->back()->with('verificationNeeded', true);
            }
        }

        return $next($request);
    }
}
