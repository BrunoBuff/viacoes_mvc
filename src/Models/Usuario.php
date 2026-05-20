<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Entidade Usuario — representa um usuário do sistema.
 *
 * Propositalmente NÃO expõe a senha como propriedade pública.
 * A hash só é acessada internamente via fromRow(), nunca serializada
 * nem exibida em views.
 */
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
   * A coluna `password` é intencionalmente ignorada aqui.
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
