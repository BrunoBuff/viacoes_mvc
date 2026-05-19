<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class UserRepository
{
  private PDO $pdo;

  public function __construct()
  {
    $this->pdo = abrirConexao();
  }

  public function findByEmail(string $email): ?array
  {
    $sql = "
            SELECT
                id,
                nome,
                email,
                password
            FROM users
            WHERE email = :email
            LIMIT 1
        ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':email', trim($email));

    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
  }
}
