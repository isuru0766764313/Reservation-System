<?php

// this class is to create middleware for customer

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;

class EnsureCustomerIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer)
        {
            return redirect()->route('login_get_route');
        }
        if (!$customer->email_verified_at)
        {
            Auth::guard('customer')->logout();
            $request->session()->put('verify_customer_id', $customer->id);
            return redirect()->route('customer.verification.notice')->withErrors(['email' => 'Please verify your email first']);
        }
        return $next($request);
    }
}
