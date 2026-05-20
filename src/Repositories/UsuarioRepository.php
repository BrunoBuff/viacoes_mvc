<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Usuario;
use PDO;

/**
 * UsuarioRepository — persistência e consulta de usuários.
 *
 * Segue o mesmo padrão do ViacaoRepository:
 *  - Recebe PDO via injeção (ou singleton via getPdo()).
 *  - Retorna objetos tipados, nunca arrays brutos.
 *  - Nunca expõe a hash de senha fora do necessário.
 */
final class UsuarioRepository
{
  private PDO $pdo;

  public function __construct(?PDO $pdo = null)
  {
    $this->pdo = $pdo ?? \getPdo();
  }

  // =========================================================
  // Consultas
  // =========================================================

  /**
   * Listagem com filtro opcional por nome ou e-mail.
   *
   * @return Usuario[]
   */
  public function all(string $busca = ''): array
  {
    $sql    = 'SELECT id, nome, email, created_at, updated_at FROM users WHERE 1=1';
    $params = [];

    if ($busca !== '') {
      $sql .= ' AND (nome LIKE :busca OR email LIKE :busca)';
      $params['busca'] = "%{$busca}%";
    }

    $sql .= ' ORDER BY nome ASC';

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return array_map(
      static fn(array $row): Usuario => Usuario::fromRow($row),
      $stmt->fetchAll(PDO::FETCH_ASSOC)
    );
  }

  /**
   * Busca um usuário pelo ID.
   * Retorna null se não existir.
   */
  public function find(int $id): ?Usuario
  {
    $stmt = $this->pdo->prepare(
      'SELECT id, nome, email, created_at, updated_at
             FROM users WHERE id = :id LIMIT 1'
    );
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? Usuario::fromRow($row) : null;
  }

  /**
   * Verifica se um e-mail já está em uso por OUTRO usuário.
   * Usado para evitar duplicatas no create e no update.
   */
  public function emailExiste(string $email, ?int $ignorarId = null): bool
  {
    $sql    = 'SELECT COUNT(*) FROM users WHERE email = :email';
    $params = ['email' => $email];

    if ($ignorarId !== null) {
      $sql .= ' AND id != :id';
      $params['id'] = $ignorarId;
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn() > 0;
  }

  // =========================================================
  // Persistência
  // =========================================================

  /**
   * Cria um novo usuário e retorna o ID gerado.
   *
   * @param string $senhaHash  Hash bcrypt gerada no Service — nunca a senha em texto plano.
   */
  public function create(string $nome, string $email, string $senhaHash): int
  {
    $stmt = $this->pdo->prepare(
      'INSERT INTO users (nome, email, password) VALUES (:nome, :email, :password)'
    );
    $stmt->execute([
      'nome'     => $nome,
      'email'    => $email,
      'password' => $senhaHash,
    ]);

    return (int) $this->pdo->lastInsertId();
  }

  /**
   * Atualiza nome e e-mail.
   * A senha só é atualizada se $novaSenhaHash não for null.
   */
  public function update(int $id, string $nome, string $email, ?string $novaSenhaHash = null): void
  {
    if ($novaSenhaHash !== null) {
      $stmt = $this->pdo->prepare(
        'UPDATE users SET nome = :nome, email = :email, password = :password WHERE id = :id'
      );
      $stmt->execute(['nome' => $nome, 'email' => $email, 'password' => $novaSenhaHash, 'id' => $id]);
    } else {
      $stmt = $this->pdo->prepare(
        'UPDATE users SET nome = :nome, email = :email WHERE id = :id'
      );
      $stmt->execute(['nome' => $nome, 'email' => $email, 'id' => $id]);
    }
  }

  /**
   * Remove um usuário pelo ID.
   *
   * Segurança: o Service verifica antes se o usuário está tentando
   * excluir a si mesmo, impedindo que o sistema fique sem admin.
   */
  public function delete(int $id): void
  {
    $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute(['id' => $id]);
  }
}
