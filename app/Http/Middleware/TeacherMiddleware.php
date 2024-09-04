<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isTeacher()) {
            $restrictedPaths = ['teachers', 'payments', 'students'];

            if (in_array($request->path(), $restrictedPaths)) {
                    return redirect('/');
            }
        }

        return $next($request);
    }
}
