<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;

/**
 * GuestMiddleware
 *
 * Protege rotas que só fazem sentido para visitantes (ex: /login).
 * Se o usuário já estiver autenticado, é redirecionado para o painel.
 *
 * — Autenticado    → redireciona para /admin/viacoes
 * — Não autenticado → invoca $next (deixa passar)
 */
final class GuestMiddleware implements MiddlewareInterface
{
  public function handle(callable $next, mixed ...$params): void
  {
    if (AuthController::check()) {
      header('Location: /admin/viacoes');
      exit;
    }

    $next(...$params);
  }
}
