<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session or use default
        $locale = session('locale', config('app.locale', 'en'));

        // Validate locale is supported
        if (!in_array($locale, ['en', 'es'])) {
            $locale = 'en';
        }

        // Set application locale
        app()->setLocale($locale);

        return $next($request);
    }
}
