<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        abort_unless($usuario !== null, 403);
        abort_unless(in_array($usuario->rol, $roles, true), 403, 'No tienes permisos para realizar esta acción.');

        return $next($request);
    }
}
