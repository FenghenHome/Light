<?php

namespace App\Http\Middleware;

use Closure;

class admin
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
        if (session('user')->level < 1)
        {
            return response([
                'success' => false,
                'message' => '权限不足'
            ]);
        }

        return $next($request);
    }
}
