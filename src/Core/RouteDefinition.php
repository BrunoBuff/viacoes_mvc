<?php
declare(strict_types=1);

namespace App\Core;

final class RouteDefinition
{
  /** @var string[] Lista de classes de middleware (FQCN). */
  public array $middlewares = [];

  public function __construct(
    public readonly string         $regex,
    public readonly array $handler,
  ) {}

  /**
   * Registra um middleware para esta rota.
   *
   * @param class-string<MiddlewareInterface> $middlewareClass
   */
  public function middleware(string $middlewareClass): static
  {
    $this->middlewares[] = $middlewareClass;
    return $this;
  }
}
