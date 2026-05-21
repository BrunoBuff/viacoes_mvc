<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\UsuarioService;
use Exception;

final class UsuarioController
{
  private UsuarioService $service;

  public function __construct(?UsuarioService $service = null)
  {
    $this->service = $service ?? new UsuarioService();
  }

  // GET /admin/usuarios
  public function index(): void
  {
    $busca = trim((string) ($_GET['busca'] ?? ''));

    $flash = View::pullFlash();

    View::render('admin/usuarios/index', [
      'usuarios' => $this->service->all($busca),
      'filtros'  => compact('busca'),
      'flash'    => $flash,
    ]);
  }

  // GET /admin/usuarios/create
  public function create(): void
  {
    View::render('admin/usuarios/create', [
      'errors' => [],
      'old'    => ['nome' => '', 'email' => ''],
    ]);
  }

  // POST /admin/usuarios
  public function store(): void
  {
    try {
      $this->service->create($_POST);
      View::flash('success', 'Usuário cadastrado com sucesso!');
      View::redirect('/admin/usuarios');
    } catch (Exception $e) {
      View::render('admin/usuarios/create', [
        'errors' => explode('|', $e->getMessage()),
        'old'    => [
          'nome'  => $_POST['nome']  ?? '',
          'email' => $_POST['email'] ?? '',
        ],
      ]);
    }
  }

  // GET /admin/usuarios/{id}/edit
  public function edit(int $id): void
  {
    $usuario = $this->service->find($id);

    if ($usuario === null) {
      http_response_code(404);
      echo 'Usuário não encontrado.';
      exit;
    }

    View::render('admin/usuarios/edit', [
      'usuario' => $usuario,
      'errors'  => [],
      'old'     => ['nome' => $usuario->nome, 'email' => $usuario->email],
    ]);
  }

  // PUT /admin/usuarios/{id}
  public function update(int $id): void
  {
    try {
      $this->service->update($id, $_POST);
      View::flash('success', 'Usuário atualizado com sucesso!');
      View::redirect('/admin/usuarios');
    } catch (Exception $e) {
      $usuario = $this->service->find($id);

      if ($usuario === null) {
        http_response_code(404);
        echo 'Usuário não encontrado.';
        exit;
      }

      View::render('admin/usuarios/edit', [
        'usuario' => $usuario,
        'errors'  => explode('|', $e->getMessage()),
        'old'     => [
          'nome'  => $_POST['nome']  ?? '',
          'email' => $_POST['email'] ?? '',
        ],
      ]);
    }
  }

  // DELETE /admin/usuarios/{id}
  public function destroy(int $id): void
  {
    try {
      $this->service->delete($id);
      View::flash('success', 'Usuário removido com sucesso!');
    } catch (Exception $e) {
      View::flash('error', $e->getMessage());
    }

    View::redirect('/admin/usuarios');
  }
}
