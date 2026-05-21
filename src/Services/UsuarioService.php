<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Usuario;
use App\Repositories\UsuarioRepository;
use App\Validators\UsuarioValidator;
use Exception;

final class UsuarioService
{
  private UsuarioRepository $repository;
  private UsuarioValidator  $validator;

  public function __construct(
    ?UsuarioRepository $repository = null,
    ?UsuarioValidator  $validator  = null
  ) {
    $this->repository = $repository ?? new UsuarioRepository();
    $this->validator  = $validator  ?? new UsuarioValidator();
  }

  /** @return Usuario[] */
  public function all(string $busca = ''): array
  {
    return $this->repository->all($busca);
  }

  public function find(int $id): ?Usuario
  {
    return $this->repository->find($id);
  }

  public function create(array $data): int
  {
    // 1. Valida formato dos campos
    $this->validator->validarCriacao($data);

    // 2. Verifica duplicidade de e-mail (responsabilidade do Service, não do Validator)
    $email = trim((string) ($data['email'] ?? ''));
    if ($this->repository->emailExiste($email)) {
      throw new Exception('Este e-mail já está sendo usado por outro usuário.');
    }

    // 3. Gera hash segura — nunca persiste senha em texto plano
    $senhaHash = password_hash(
      trim((string) $data['password']),
      PASSWORD_BCRYPT,
      ['cost' => 12]
    );

    return $this->repository->create(
      trim((string) $data['nome']),
      $email,
      $senhaHash
    );
  }

  public function update(int $id, array $data): void
  {
    $usuario = $this->repository->find($id);

    if ($usuario === null) {
      throw new Exception('Usuário não encontrado.');
    }

    // 1. Valida formato
    $this->validator->validarEdicao($data);

    // 2. Verifica duplicidade ignorando o próprio usuário
    $email = trim((string) ($data['email'] ?? ''));
    if ($this->repository->emailExiste($email, $id)) {
      throw new Exception('Este e-mail já está sendo usado por outro usuário.');
    }

    // 3. Gera nova hash só se o admin quis alterar a senha
    $novaSenhaHash = null;
    $password      = trim((string) ($data['password'] ?? ''));

    if ($password !== '') {
      $novaSenhaHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    $this->repository->update(
      $id,
      trim((string) $data['nome']),
      $email,
      $novaSenhaHash
    );
  }

  public function delete(int $id): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    // CORREÇÃO: chave correta é 'user_id', não 'usuario_id'
    // A versão anterior nunca bloqueava autoexclusão por ler a chave errada.
    $usuarioLogadoId = (int) ($_SESSION['user_id'] ?? 0);

    if ($id === $usuarioLogadoId) {
      throw new Exception('Você não pode excluir a sua própria conta.');
    }

    if ($this->repository->find($id) === null) {
      throw new Exception('Usuário não encontrado.');
    }

    $this->repository->delete($id);
  }
}
