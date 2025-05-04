<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class VerifyCheckout
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
        $user = Auth::user();
        
        // Check if email verification is required and if the email is not verified
        if (Schema::hasColumn('users', 'email_verified_at') && $user->email_verified_at === null) {
            return redirect()->route('cart')->with('verificationNeeded', true);
        }
        
        return $next($request);
    }
}
