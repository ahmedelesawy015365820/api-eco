<?php

namespace App\Http\Middleware;

use App\Traits\Message;
use Closure;
use Illuminate\Http\Request;

class EmployeeApiMiddleware
{
    use Message;

    public function handle(Request $request, Closure $next)
    {
        if(auth()->guard('api')->user()->auth == 3){
            return $this->sendError('You do not have access to');
        }
        return $next($request);
    }
}