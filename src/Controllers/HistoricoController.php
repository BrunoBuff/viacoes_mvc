<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\HistoricoRepository;

// Exibe o log das alterações realizadas
final class HistoricoController
{
  public function __construct()
  {
    if (empty($_SESSION['logado'])) {                   // verificar se usuário está logado
      View::redirect('/login');                    // redireciona para login
    }
  }

  // ── GET /admin/historico ────────────────────────────────────────────────

  public function index(): void
  {
    $repo = new HistoricoRepository(getPdo());           // criar instancia do repository
    $logs = $repo->findRecent(100);                      // buscar os 100 logs recentes

    View::render('admin/historico/index', [        // renderizar página do histórico
      'logs' => $logs,                                  // enviar logs para view
    ]);
  }
}
