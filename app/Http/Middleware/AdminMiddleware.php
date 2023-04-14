<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('api')->check()) {

            if (Auth::guard('api')->user()->role == 'admin') {
                return $next($request);
            } else {
                return response()->json(["message"=>'only admins are allowed'], 200, );
            }

        } else {
             return response()->json(['message'=>'Cant access Login first'], 200 );
        }




        return $next($request);
    }
}
