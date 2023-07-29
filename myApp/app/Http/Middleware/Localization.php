<?php

namespace App\Http\Middleware;

use Closure;
use App;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       // Check header request and determine localizaton
      // dump($request);
       $local = ($request->hasHeader('localization')) ? $request->header('localization') : 'en';       
       // set laravel localization
      App::setLocale($local);
      // continue request
      return $next($request);
    }
}
