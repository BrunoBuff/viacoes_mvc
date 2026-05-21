<?php
declare(strict_types=1);

namespace App\Models;

final class Usuario
{
  public function __construct(
    public readonly int     $id,
    public readonly string  $nome,
    public readonly string  $email,
    public readonly string  $criadoEm,
    public readonly string  $atualizadoEm,
  ) {}

  /**
   * Converte o array bruto do PDO em um objeto tipado.
   * A coluna `password` é ignorada
   */
  public static function fromRow(array $row): self
  {
    return new self(
      id:            (int)    $row['id'],
      nome:          (string) $row['nome'],
      email:         (string) $row['email'],
      criadoEm:      (string) ($row['created_at'] ?? ''),
      atualizadoEm:  (string) ($row['updated_at'] ?? ''),
    );
  }
}
