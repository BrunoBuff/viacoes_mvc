<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Contrato para todos os middlewares da aplicação.
 *
 * Cada middleware recebe um callable $next que representa
 * o próximo passo da cadeia (controller, outro middleware, etc.).
 * Se o middleware bloquear a requisição, ele simplesmente não
 * invoca $next e encerra a execução (redirect / abort).
 */
interface MiddlewareInterface
{
  /**
   * @param callable $next  Próximo handler da cadeia.
   * @param mixed    ...$params  Parâmetros de rota (ex: id).
   */
  public function handle(callable $next, mixed ...$params): void;
}
