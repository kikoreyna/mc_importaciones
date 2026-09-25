<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePackageManager
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isPackageManager(), 403, 'No tienes permisos para modificar guías.');

        return $next($request);
    }
}