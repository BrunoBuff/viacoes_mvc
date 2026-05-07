<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\ViacaoRepository;

// Controla a página pública inicial do site. Exibe as viações ativas.
final class HomeController                                                               // controlar página inicial pública do sistema
{
  public function index(): void
  {
    $viacoes = (new ViacaoRepository(getPdo()))->allAtivas();                            // buscar viações ativas no banco

    View::render('home/index', [                                                   // renderiza a página inicial
      'viacoes' => $viacoes,                                                            // enviar lista de viações para view
    ]);
  }
}
