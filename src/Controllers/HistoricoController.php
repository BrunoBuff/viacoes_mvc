<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\HistoricoRepository;

final class HistoricoController
{
  public function index(): void
  {
    $repo = new HistoricoRepository();

    $filtros = [
      'viacao_id' => trim((string) ($_GET['viacao_id'] ?? '')),
      'user_id'   => trim((string) ($_GET['user_id']   ?? '')),
      'acao'      => trim((string) ($_GET['acao']       ?? '')),
      'data_ini'  => trim((string) ($_GET['data_ini']   ?? '')),
      'data_fim'  => trim((string) ($_GET['data_fim']   ?? '')),
    ];

    View::render('admin/historico/index', [
      'historico' => $repo->all($filtros),
      'viacoes'   => $repo->listViacoes(),
      'usuarios'  => $repo->listUsuarios(),
      'filtros'   => $filtros,
    ]);
  }
}
