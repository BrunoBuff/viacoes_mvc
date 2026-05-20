<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;
use Exception;

/**
 * Controller da Página Inicial.
 *
 * OBJETIVO PRINCIPAL — Sistema de filtro de viações:
 *  Permite ao visitante filtrar viações por nome/cidade via GET sem page reload
 *  completo (o JS do front-end envia o termo e a página re-renderiza a grid).
 *
 * CORREÇÕES:
 *  - Cache só é usado quando não há filtro ativo (comportamento correto).
 *  - O $cacheHit era lido ANTES de chamar $this->viacoes->all() e portanto
 *    sempre refletia o estado anterior à possível reconstrução do cache —
 *    corrigido para ler depois.
 *  - Passa $filtro para a view para repovoar o campo de busca.
 */
final class HomeController
{
  private ViacaoService $viacoes;

  public function __construct(?ViacaoService $viacoes = null)
  {
    $this->viacoes = $viacoes ?? new ViacaoService();
  }

  public function index(): void
  {
    // Termo de filtro vindo do campo de busca da home
    $filtro = trim((string) ($_GET['filtro'] ?? ''));

    try {
      // Com filtro ativo: busca direta (sem cache, qualquer status ativo)
      // Sem filtro: aproveita cache de viações ativas
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
