<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Читаємо заголовок (за замовчуванням 'de' або 'uk')
        $lang = $request->header('Accept-Language', 'de');

        $supportedLanguages = ['uk', 'en', 'de', 'ru'];

        // Якщо мова підтримується — встановлюємо її
        if (in_array($lang, $supportedLanguages)) {
            App::setLocale($lang);
        }

        return $next($request);
    }
}
