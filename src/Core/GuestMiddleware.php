<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;

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
