<?php
declare(strict_types=1);

namespace App\Models;

final class Historico
{
  public function __construct(
    public readonly int     $id,
    public readonly ?int    $viacaoId,
    public readonly ?int    $userId,
    public readonly string  $acao,
    public readonly ?string $antes,
    public readonly ?string $depois,
    public readonly string  $criadoEm,
    // JOINs opcionais — podem ser null se o registro foi deletado
    public readonly ?string $viacaoNome,
    public readonly ?string $usuarioNome,
    public readonly ?string $usuarioEmail,
  ) {}

  public static function fromRow(array $row): self
  {
    return new self(
      id:            (int) $row['id'],
      viacaoId:      isset($row['viacao_id'])    ? (int) $row['viacao_id']    : null,
      userId:        isset($row['user_id'])       ? (int) $row['user_id']      : null,
      acao:          (string) ($row['acao']       ?? ''),
      antes:         $row['antes']                ?? null,
      depois:        $row['depois']               ?? null,
      criadoEm:      (string) ($row['criado_em'] ?? ''),
      viacaoNome:    $row['viacao_nome']          ?? null,
      usuarioNome:   $row['usuario_nome']         ?? null,
      usuarioEmail:  $row['usuario_email']        ?? null,
    );
  }

  /**
   * Decodifica o snapshot JSON do campo 'antes' ou 'depois'
   * e retorna um array associativo (ou null se vazio/inválido).
   */
  public function antesArray(): ?array
  {
    return $this->antes !== null ? json_decode($this->antes, true) : null;
  }

  public function depoisArray(): ?array
  {
    return $this->depois !== null ? json_decode($this->depois, true) : null;
  }
}
