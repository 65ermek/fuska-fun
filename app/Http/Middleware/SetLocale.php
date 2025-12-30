<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next, $forcedLocale = null)
    {
        $supported = config('locales.supported', ['cs']);
        $default   = config('locales.default', 'cs');

        // 1️⃣ Если передан принудительный язык (например, в роуте set.locale)
        if ($forcedLocale && in_array($forcedLocale, $supported, true)) {
            App::setLocale($forcedLocale);
            Session::put('locale', $forcedLocale);
            return $next($request);
        }

        // 2️⃣ Если есть язык в сессии (выбран вручную)
        if (Session::has('locale') && in_array(Session::get('locale'), $supported, true)) {
            App::setLocale(Session::get('locale'));
            return $next($request);
        }

        // 3️⃣ Автоопределение по браузеру
        $browserLocales = explode(',', $request->server('HTTP_ACCEPT_LANGUAGE', ''));
        $detected = null;

        foreach ($browserLocales as $lang) {
            $code = substr(trim(explode(';', $lang)[0]), 0, 2);
            if (in_array($code, $supported, true)) {
                $detected = $code;
                break;
            }
        }

        $locale = $detected ?: $default;

        // 4️⃣ Устанавливаем и запоминаем
        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }
}
