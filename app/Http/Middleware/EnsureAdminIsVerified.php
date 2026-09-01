<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin)
        {
            return redirect()->route('admin.login.get.route');
        }
        if (!$admin->email_verified_at)
        {
            Auth::guard('admin')->logout();
            $request->session()->put('verify_admin_id', $admin->id);
            return redirect()->route('admin.verification.notice')->withErrors(['email' => 'Please verify your email first']);
        }
        return $next($request);
    }
}
