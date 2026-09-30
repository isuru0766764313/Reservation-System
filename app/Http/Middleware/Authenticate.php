<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson())
        {
            // Store intended URL before redirecting to login
            if ($request->route())
            {
                $request->session()->put('url.intended', $request->url());
            }
            // For admin guard
            if ($request->is('admin/*'))
            {
                return route('admin.login.get.route');
            }
            // For customer guard
            elseif ($request->is('customer/*'))
            {
                // Flag the home page so it can auto-slide-in the sign-in panel
                $request->session()->put('auth_redirected', true);
                return route('login_get_route');
            }
            // Fallback to customer login
            $request->session()->put('auth_redirected', true);
            return route('login_get_route');
        }
        else
        {
            // to eliminate 419|page expired
            return route('home_route');
        }
    }
}
