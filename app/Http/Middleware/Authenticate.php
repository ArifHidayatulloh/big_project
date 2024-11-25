<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/loginPage')->withErrors(['error' => 'You must be logged in to access this page.'])->withInput();
        }
        return $next($request);
    }
}
