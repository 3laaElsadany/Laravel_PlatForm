<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SetFilamentLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            // Keep Filament navigation on the right (RTL layout).
            $locale = 'ar';

            app()->setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
