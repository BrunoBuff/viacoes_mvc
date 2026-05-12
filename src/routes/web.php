<?php
declare(strict_types=1);

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ViacaoController;
use App\Controllers\HistoricoController;
use App\Controllers\AuthController;

$router = new Router();

// ── Pública ───────────────────────────────────────────────
$router->get('/', [HomeController::class, 'index']);

// ── Autenticação ──────────────────────────────────────────
$router->get('/login',  [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// ── Admin — Viações ───────────────────────────────────────
$router->get('/admin/viacoes',           [ViacaoController::class, 'index']);
$router->get('/admin/viacoes/create',    [ViacaoController::class, 'create']);
$router->post('/admin/viacoes',          [ViacaoController::class, 'store']);
$router->get('/admin/viacoes/{id}/edit', [ViacaoController::class, 'edit']);
$router->post('/admin/viacoes/{id}',     [ViacaoController::class, 'update']);
$router->delete('/admin/viacoes/{id}',   [ViacaoController::class, 'destroy']);

// ── Admin — Histórico ─────────────────────────────────────
$router->get('/admin/historico', [HistoricoController::class, 'index']);

return $router;
