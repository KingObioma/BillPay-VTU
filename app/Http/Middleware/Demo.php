<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Demo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $methods = ['POST','PUT','PATCH','DELETE'];
        if(env('IS_DEMO') == true && in_array(request()->method(), $methods)){
            return back()->with('alert', 'This is DEMO version. You can just explore all the features but can\'t take any action.');
        }
        return $next($request);
    }
}
