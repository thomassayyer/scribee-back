<?php

namespace App\Http\Middleware;

use Closure;

class HandleCors
{
    /**
     * The headers allowing the incomming requests.
     * 
     * @var array
     */
    private $headers = [
        'Access-Control-Allow-Methods' => ['HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
        'Access-Control-Allow-Headers' => ['*'],
        'Access-Control-Allow-Origin' => ['http://localhost:8080'],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $request->isMethod('OPTIONS') ? response('OK', 200) : $next($request);

        foreach ($this->headers as $key => $value) {
            $response->header($key, implode(',', $value));
        }

        return $response;
    }
}
