<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;

/**
 * AuthMiddleware
 *
 * Bloqueia o acesso a rotas privadas para usuários não autenticados.
 * Ao ser invocado, verifica a sessão via AuthController::check().
 *
 * — Autenticado   → invoca $next (deixa passar)
 * — Não autenticado → armazena a URL pretendida em sessão e redireciona
 *                     para /login com mensagem de aviso.
 */
final class AuthMiddleware implements MiddlewareInterface
{
  public function handle(callable $next, mixed ...$params): void
  {
    if (!AuthController::check()) {

      // Salva a URL que o usuário tentou acessar para
      // redirecionar de volta após o login bem-sucedido.
      if (session_status() === PHP_SESSION_NONE) {
        session_start();
      }

      $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '/admin/viacoes';
      $_SESSION['auth_notice']  = 'Faça login para acessar esta página.';

      header('Location: /login');
      exit;
    }

    // Sessão válida: continua para o controller.
    $next(...$params);
  }
}
