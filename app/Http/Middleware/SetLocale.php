<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = ['en', 'ar'];

        $locale = $request->query('lang');
        if (! is_string($locale)) {
            $locale = null;
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $storedLocale = session('locale', config('app.locale'));
            $locale = is_string($storedLocale) ? $storedLocale : config('app.locale');
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = 'en';
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);
        URL::defaults(['lang' => $locale]);

        if (
            $request->isMethod('GET')
            && ! $request->has('lang')
            && ! $request->expectsJson()
            && ! $request->ajax()
            && ! $request->routeIs('locale.switch')
        ) {
            return redirect()->to($request->fullUrlWithQuery(['lang' => $locale]));
        }
        
        return $next($request);
    }
}
