<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAmbiente
{
    public function handle(Request $request, Closure $next, string $ambiente): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Nao autenticado.',
                ], 401);
            }

            return redirect('/login');
        }

        if (session('ambiente') !== $ambiente) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Selecione o ambiente correto para acessar este recurso.',
                ], 403);
            }

            return redirect('/selecionar-ambiente')->with('error', 'Selecione o ambiente para continuar.');
        }

        return $next($request);
    }
}
