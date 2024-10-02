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

      
      if (in_array($request->locale, ['en', 'fr', 'ar']))
      \Illuminate\Support\Facades\App::setLocale($request->locale);

      if (in_array(auth()?->user()?->locale, ['en', 'fr', 'ar']))
      \Illuminate\Support\Facades\App::setLocale(auth()->user()->locale);
  
        return $next($request);
    }
}
