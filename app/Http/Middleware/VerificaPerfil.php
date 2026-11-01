<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificaPerfil
{
    public function handle(Request $request, Closure $next, string ...$perfis): Response
    {
        abort_unless(in_array($request->user()?->perfil, $perfis, true), 403);

        return $next($request);
    }
}
