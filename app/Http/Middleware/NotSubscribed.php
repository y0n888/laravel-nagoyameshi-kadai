<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (!$request->user()) {
        //     return redirect('login'); 
        // }

        if ($request->user()?->subscribed('premium_plan')) {
            if (!in_array($request->route()->getName(), ['subscription.destroy', 'subscription.update'])) {
            return redirect('subscription/edit');
            }
        }

        return $next($request);
    }
}
