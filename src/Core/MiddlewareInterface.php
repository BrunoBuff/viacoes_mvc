<?php
declare(strict_types=1);

namespace App\Core;

interface MiddlewareInterface
{
  /**
   * @param callable $next  Próximo handler da cadeia.
   * @param mixed    ...$params  Parâmetros de rota (ex: id).
   */
  public function handle(callable $next, mixed ...$params): void;
}
