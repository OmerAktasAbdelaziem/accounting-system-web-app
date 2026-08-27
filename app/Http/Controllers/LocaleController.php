<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch($locale)
    {
        if (in_array($locale, ['en', 'ar', 'tr'], true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
            app('translator')->setFallback($locale);
        } else {
            $locale = 'en';
        }

        $targetUrl = url()->previous();

        if (preg_match('/([?&])lang=[^&]*/', $targetUrl)) {
            $targetUrl = preg_replace('/([?&])lang=[^&]*/', '$1lang=' . $locale, $targetUrl);
        } else {
            $targetUrl .= (str_contains($targetUrl, '?') ? '&' : '?') . 'lang=' . $locale;
        }

        return redirect()->to($targetUrl);
    }
}
