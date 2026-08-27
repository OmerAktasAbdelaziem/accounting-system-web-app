<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = ['en', 'ar', 'tr'];

        $locale = $request->query('lang');
        if (! is_string($locale)) {
            $locale = null;
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $storedLocale = session('locale', Setting::get('language', config('app.locale')));
            $locale = is_string($storedLocale) ? $storedLocale : config('app.locale');
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = 'en';
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);
        app('translator')->setFallback($locale);
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
