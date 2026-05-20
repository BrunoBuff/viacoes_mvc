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
  private UsuarioValidator $validator;

  public function __construct(
    ?UsuarioRepository $repository = null,
    ?UsuarioValidator $validator = null
  ) {
    $this->repository = $repository ?? new UsuarioRepository();
    $this->validator = $validator ?? new UsuarioValidator($this->repository);
  }

  /**
   * Retorna todos os usuários filtrados.
   * @return Usuario[]
   */
  public function all(string $busca = ''): array
  {
    return $this->repository->all($busca);
  }

  /**
   * Busca um usuário pelo ID.
   */
  public function find(int $id): ?Usuario
  {
    return $this->repository->find($id);
  }

  /**
   * Regra de negócio para criar um usuário.
   */
  public function create(array $data): int
  {
    // 1. Valida as entradas do formulário
    $this->validator->validar($data);

    // 2. Gera a hash segura da senha antes de mandar para a persistência
    $senhaHash = password_hash($data['password'], PASSWORD_BCRYPT);

    // 3. Salva no banco de dados
    return $this->repository->create(
      trim($data['nome']),
      trim($data['email']),
      $senhaHash
    );
  }

  /**
   * Regra de negócio para atualizar um usuário.
   */
  public function update(int $id, array $data): void
  {
    // 1. Valida passando o ID atual para permitir que o usuário mantenha o mesmo e-mail dele
    $this->validator->validar($data, $id);

    $novaSenhaHash = null;
    $password = (string) ($data['password'] ?? '');

    // 2. Se o usuário digitou uma nova senha na edição, gera uma nova hash
    if ($password !== '') {
      $novaSenhaHash = password_hash($password, PASSWORD_BCRYPT);
    }

    // 3. Atualiza os dados através do Repositório
    $this->repository->update(
      $id,
      trim($data['nome']),
      trim($data['email']),
      $novaSenhaHash
    );
  }

  /**
   * Regra de negócio para exclusão (com trava de segurança).
   */
  public function delete(int $id): void
  {
    // Garante que a sessão está ativa para ler quem está logado
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    // Pega o ID do usuário atualmente logado no sistema
    $usuarioLogadoId = (int) ($_SESSION['usuario_id'] ?? 0);

    // Impede a autoexclusão catastrófica
    if ($id === $usuarioLogadoId) {
      throw new Exception('Ação bloqueada: Você não pode excluir a sua própria conta administrativa.');
    }

    $this->repository->delete($id);
  }
}
