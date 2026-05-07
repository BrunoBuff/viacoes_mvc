<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\HistoricoRepository;
use App\Repositories\ViacaoRepository;
use App\Services\ViacaoService;

// Gerencia as ações de CRUD das viações. Rotas protegidas por sessão — redireciona para /login se não autenticado.
final class ViacaoController
{
  private readonly ViacaoService $service;                     // guarda instancia do service

  public function __construct()
  {
    $this->requireAuth();                                      // verifica se usuário está logado

    $pdo           = getPdo();
    $this->service = new ViacaoService(                        // instanciar service e repositories
      new ViacaoRepository($pdo),
      new HistoricoRepository($pdo),
    );
  }

  // ── GET /admin/viacoes ─────────────────────────────────────────────────────────────────────

  public function index(): void
  {
    $busca   = trim((string) ($_GET['busca'] ?? ''));           // pegar busca da url
    $viacoes = $this->service->listar($busca);                  // lista viações filtrando pela busca

    View::render('admin/viacoes/index', [                 // renderizar a pagina de listagem
      'viacoes' => $viacoes,
      'busca'   => $busca,
    ]);
  }

  // ── GET /admin/viacoes/create ──────────────────────────────────────────────────────────────

  public function create(): void
  {
    View::render('admin/viacoes/create', [                // abrir formulário de cadastro
      'errors' => [],
      'old'    => [],
    ]);
  }

  // ── POST /admin/viacoes ────────────────────────────────────────────────────────────────────

  public function store(): void
  {
    $arquivo = !empty($_FILES['logo']['name']) ? $_FILES['logo'] : null;         // verificar se usuário enviou arquivo
    $result  = $this->service->criar($_POST, $arquivo);                          // enviar dados para service criar viação

    if (!$result['ok']) {                                                        // verificar se houve erro
      View::render('admin/viacoes/create', [                               // renderizar formulário novamente
        'errors' => $result['errors'],                                          // enviar erros para view
        'old'    => $_POST,                                                     // manter dados digitados
      ]);
      return;
    }

    View::flash('success', 'Viação cadastrada com sucesso!');      // mensagem de sucesso
    View::redirect('/admin/viacoes');                                      // redireciona para listagem
  }

  // ── GET /admin/viacoes/{id}/edit ───────────────────────────────────────────────────────────

  public function edit(int $id): void
  {
    $viacao = $this->service->buscarPorId($id);                    // buscar viação pelo id

    if ($viacao === null) {                                        // verifica se viação existe, se não existir redireciona para listagem
      View::redirect('/admin/viacoes');
      return;
    }

    View::render('admin/viacoes/edit', [                     // abre o formulário de edição
      'viacao' => $viacao,
      'errors' => [],
      'old'    => [
        'nome'   => $viacao->nome,
        'url'    => $viacao->url,
        'cidade' => $viacao->cidade,
        'status' => $viacao->status,
      ],
    ]);
  }

  // ── POST /admin/viacoes/{id} ───────────────────────────────────────────────────────────────

  public function update(int $id): void
  {
    $viacao = $this->service->buscarPorId($id);                                 // busca viacao atual

    if ($viacao === null) {                                                     // verifica se viação existe, se não, retorna para listagem
      View::redirect('/admin/viacoes');
      return;
    }

    $arquivo = !empty($_FILES['logo']['name']) ? $_FILES['logo'] : null;         // verificar se usuário enviou nova logo
    $result  = $this->service->atualizar($id, $viacao, $_POST, $arquivo);        // enviar dados para service atualizar viação

    if (!$result['ok']) {                                                       // verifica se houve erro
      View::render('admin/viacoes/edit', [                                // renderiza novamente
        'viacao' => $viacao,
        'errors' => $result['errors'],                                         // enviar erros
        'old'    => $_POST,                                                    // manter dados digitados
      ]);
      return;
    }

    View::flash('success', 'Viação atualizada com sucesso!');    // mensagem de sucesso
    View::redirect('/admin/viacoes');                                    // retorna para listagem
  }

  // ── POST /admin/viacoes/{id}/delete ───────────────────────────────────────────────────────

  public function destroy(int $id): void
  {
    $viacao = $this->service->buscarPorId($id);                                       // busca viação pelo id

    if ($viacao !== null) {                                                           // verifica viação existente
      $this->service->excluir($viacao);                                               // exclui viação
      View::flash('success', "Viação \"{$viacao->nome}\" excluída.");   // mensagem de sucesso
    }

    View::redirect('/admin/viacoes');                                           // volta para listagem
  }

  // ── Auth helper ────────────────────────────────────────────────────────────────────────────

  private function requireAuth(): void
  {
    if (empty($_SESSION['logado'])) {       // verificar se usuário não está logado
      View::redirect('/login');       // redirecionar para login
    }
  }
}
