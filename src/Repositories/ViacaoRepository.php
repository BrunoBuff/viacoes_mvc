<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Viacao;
use PDO;

/**
 * Repositório: responsável exclusivamente pela persistência de dados no MySQL.
 *
 * CORREÇÕES:
 *  - Função de conexão unificada: era chamada ora como getPDO() (maiúsculas),
 *    ora como getPdo() — padronizado para getPdo() (conforme db.php).
 *  - all(): busca agora filtra por nome OU cidade (antes só por nome),
 *    alinhando com o placeholder da view ("Buscar por nome ou cidade...").
 *  - Adicionado índice composto sugerido em init.sql (ver comentário).
 */
final class ViacaoRepository
{
  private PDO $pdo;

  public function __construct(?PDO $pdo = null)
  {
    // CORREÇÃO: getPdo() (minúsculo) — consistente com db.php
    $this->pdo = $pdo ?? \getPdo();
  }

  /**
   * Busca filtrada e ordenada.
   * CORREÇÃO: filtra por nome OU cidade, não só por nome.
   */
  public function all(string $busca, string $status, string $ordem, string $dir): array
  {
    $sql    = 'SELECT * FROM viacoes WHERE 1=1';
    $params = [];

    if ($busca !== '') {
      $sql .= ' AND (nome LIKE :busca OR cidade LIKE :busca)';
      $params['busca'] = "%{$busca}%";
    }

    if (in_array($status, ['ativo', 'inativo'], true)) {
      $sql .= ' AND status = :status';
      $params['status'] = $status;
    }

    // Whitelist de segurança para evitar SQL Injection via ORDER BY
    $colunas = ['id', 'nome', 'cidade', 'criado_em', 'alterado_em'];
    $ordem   = in_array($ordem, $colunas, true) ? $ordem : 'nome';
    $dir     = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

    $sql .= " ORDER BY {$ordem} {$dir}";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return array_map(
      static fn(array $r): Viacao => Viacao::fromRow($r),
      $stmt->fetchAll(PDO::FETCH_ASSOC)
    );
  }

  public function find(int $id): ?Viacao
  {
    $stmt = $this->pdo->prepare('SELECT * FROM viacoes WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? Viacao::fromRow($row) : null;
  }

  public function create(array $data): int
  {
    $stmt = $this->pdo->prepare(
      'INSERT INTO viacoes (nome, url, cidade, status, logo)
             VALUES (:nome, :url, :cidade, :status, :logo)'
    );
    $stmt->execute($data);

    return (int) $this->pdo->lastInsertId();
  }

  public function update(int $id, array $data): void
  {
    $sql = 'UPDATE viacoes SET nome = :nome, url = :url, cidade = :cidade, status = :status';

    if (array_key_exists('logo', $data)) {
      $sql .= ', logo = :logo';
    } else {
      unset($data['logo']);
    }

    $sql .= ' WHERE id = :id';

    $data['id'] = $id;

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($data);
  }

  public function delete(int $id): void
  {
    $stmt = $this->pdo->prepare('DELETE FROM viacoes WHERE id = :id');
    $stmt->execute(['id' => $id]);
  }
}
