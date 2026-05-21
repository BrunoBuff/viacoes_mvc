<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Engine de Roteamento — mapeia URIs para Controllers.
 *
 * CORREÇÕES:
 *  - Os métodos get/post/put/delete agora retornam RouteDefinition para
 *    permitir o encadeamento de middlewares (->middleware(AuthMiddleware::class)).
 */
final class Router
{
  /** @var array<string, RouteDefinition[]> */
  private array $routes = [];

  public function get(string $pattern, callable|array $handler): RouteDefinition
  {
    return $this->add('GET', $pattern, $handler);
  }

  public function post(string $pattern, callable|array $handler): RouteDefinition
  {
    return $this->add('POST', $pattern, $handler);
  }

  public function put(string $pattern, callable|array $handler): RouteDefinition
  {
    return $this->add('PUT', $pattern, $handler);
  }

  public function delete(string $pattern, callable|array $handler): RouteDefinition
  {
    return $this->add('DELETE', $pattern, $handler);
  }

  private function add(string $method, string $pattern, callable|array $handler): RouteDefinition
  {
    $pattern    = '/' . ltrim($pattern, '/');
    $definition = new RouteDefinition($this->patternToRegex($pattern), $handler);

    $this->routes[strtoupper($method)][] = $definition;

    return $definition;
  }

  // Resolve a requisição e invoca o handler correspondente.
  public function dispatch(string $method, string $uri): void
  {
    $method = $this->resolveMethod($method);
    $path   = '/' . ltrim((string) parse_url($uri, PHP_URL_PATH), '/');
    $routes = $this->routes[$method] ?? [];

    if (empty($routes)) {
      $this->abortNotFound();
      return;
    }

    foreach ($routes as $route) {
      if (!preg_match($route->regex, $path, $matches)) {
        continue;
      }

      $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

      if (isset($params['id']) && ctype_digit((string) $params['id'])) {
        $params['id'] = (int) $params['id'];
      }

      $this->runMiddlewareChain($route, $params);
      return;
    }

    $this->abortNotFound();
  }

  /**
   * Executa a cadeia de middlewares e, ao final, o handler da rota.
   */
  private function runMiddlewareChain(RouteDefinition $route, array $params): void
  {
    $handler = fn() => $this->invokeHandler($route->handler, $params);

    // Constrói a cadeia na ordem inversa (onion model)
    $chain = array_reduce(
      array_reverse($route->middlewares),
      function (callable $next, string $middlewareClass) use ($params): callable {
        return function () use ($middlewareClass, $next, $params): void {
          /** @var MiddlewareInterface $middleware */
          $middleware = new $middlewareClass();
          $middleware->handle($next, ...array_values($params));
        };
      },
      $handler
    );

    $chain();
  }

  private function invokeHandler(callable|array $handler, array $params): void
  {
    if (is_array($handler)) {
      $controller = new $handler[0]();
      $controller->{$handler[1]}(...array_values($params));
      return;
    }

    $handler(...array_values($params));
  }

  private function abortNotFound(): void
  {
    http_response_code(404);
    echo 'Página não encontrada.';
    exit;
  }

  private function resolveMethod(string $method): string
  {
    if ($method === 'POST' && isset($_POST['_method'])) {
      $spoofed = strtoupper($_POST['_method']);
      if (in_array($spoofed, ['PUT', 'DELETE', 'PATCH'], true)) {
        return $spoofed;
      }
    }

    return strtoupper($method);
  }

  private function patternToRegex(string $pattern): string
  {
    $regex = preg_replace_callback(
      '#\{([a-zA-Z0-9_]+)\}#',
      static fn(array $m): string => $m[1] === 'id'
        ? '(?P<id>\d+)'
        : '(?P<' . $m[1] . '>[^/]+)',
      $pattern
    );

    return '#^' . $regex . '/?$#';
  }
}
