<?php

namespace App\Http\Middleware;

use App\Traits\Message;
use Closure;
use Illuminate\Http\Request;

class secretApiMiddleware
{
    use Message;

    public function handle(Request $request, Closure $next)
    {

        if($request->api_password !== env('API_PASSWORD','Snr92EUKCmrE06PiJ')){
            return $this->sendError('UnAuthenticated');
        }
        return $next($request);
    }
}