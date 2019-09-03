<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleUserScore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $min
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $min = 1)
    {
        if ($request->user()->score < $min) {
            return response('Your score is too low!', 401);
        }

        return $next($request);
    }
}
