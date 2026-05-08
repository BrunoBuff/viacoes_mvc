<?php

declare(strict_types=1);

namespace App\Core;

//Router para registrar rotas GET e POST e despachar handlers

final class Router
{
  // ── Armazenamento interno das rotas ─────────────────────────────────────

  private array $routes = [];       //armazenar todas as rotas registradas

  // ── Registro de rotas GET/POST ──────────────────────────────────────────

  /** Registra uma rota GET */
  public function get(string $pattern, callable|array $handler): void
  {
    $this->add('GET', $pattern, $handler);
  }

  /** Registra uma rota POST */
  public function post(string $pattern, callable|array $handler): void
  {
    $this->add('POST', $pattern, $handler);
  }

  // ── Registro interno da rota ────────────────────────────────────────────

  /** Adiciona uma rota na tabela interna e converte pattern para regex. */

  private function add(string $method, string $pattern, callable|array $handler): void
  {
    $method = strtoupper($method);
    $pattern = $this->normalizaPath($pattern);

    $this->routes[$method] ??= [];

    $this->routes[$method][] = [
      'pattern' => $pattern,
      'regex'   => $this->patternToRegex($pattern),
      'handler' => $handler,
    ];
  }

  // ── Resolver rota atual ─────────────────────────────────────────────────

  /** Resolve a rota e executa o handler correspondente. */
  public function dispatch(string $metod, string $url): void
  {
      $method = strtoupper($metod);

      $path = parse_url($uri, PHP_URL_PATH);
      $path = is_string($path) ? $path : '/';
      $path = $this->normalizePath($path);

      foreach ($this->routes[$method] ?? [] as $route) {
        $matches = [];

        if (preg_match($route['regex'], $path, $matches) !== 1) {
          continue;
        }

        $params = [];

        foreach ($matches as $key => $)

      }
  }

}

