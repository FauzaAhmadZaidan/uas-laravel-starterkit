<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Symfony\Component\HttpFoundation\Response;

class Role
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!in_array(Auth::user()->role, $roles)) { // Ganti auth() dengan Auth::
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}