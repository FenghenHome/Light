<?php

namespace App\Http\Middleware;

use Closure;

class Login
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
        if (!session()->has('user'))
        {
            return response([
                'success' => false,
                'message' => '没有登录'
            ]);
        }

        return $next($request);
    }
}
