<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;
use Exception;

final class HomeController
{
  private ViacaoService $viacoes;

  public function __construct(?ViacaoService $viacoes = null)
  {
    $this->viacoes = $viacoes ?? new ViacaoService();
  }

  public function index(): void
  {

    $filtro = trim((string) ($_GET['filtro'] ?? ''));

    try {

      $viacoesAtivas = $this->viacoes->all(
        busca:  $filtro,
        status: 'ativo',
        ordem:  'nome',
        dir:    'ASC'
      );

      $cacheHit = ($filtro === '') && (\getCachedData('viacoes_ativas') !== null);

      View::render('home', [
        'viacoesAtivas' => $viacoesAtivas,
        'filtro'        => $filtro,
        'erroConexao'   => false,
        'cacheHit'      => $cacheHit,
      ]);

    } catch (Exception) {
      View::render('home', [
        'viacoesAtivas' => [],
        'filtro'        => $filtro,
        'erroConexao'   => true,
        'cacheHit'      => false,
      ]);
    }
  }
}
