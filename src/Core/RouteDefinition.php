<?php
declare(strict_types=1);

namespace App\Core;

/**
 * RouteDefinition
 *
 * Objeto imutável (exceto pela lista de middlewares) que encapsula
 * os dados de uma rota registrada no Router.
 *
 * O método middleware() permite encadear middlewares de forma fluente:
 *
 *   $router->get('/admin/viacoes', [...])
 *          ->middleware(AuthMiddleware::class)
 *          ->middleware(LogMiddleware::class);
 */
final class RouteDefinition
{
  /** @var string[] Lista de classes de middleware (FQCN). */
  public array $middlewares = [];

  public function __construct(
    public readonly string         $regex,
    public readonly callable|array $handler,
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
