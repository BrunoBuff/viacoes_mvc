<?php
declare(strict_types=1);
namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Services\ViacaoService;
use App\Repositories\ViacaoRepository;
use App\Repositories\HistoricoRepository;
use App\Validators\ViacaoValidator;
use App\Models\Viacao;

class ViacaoServiceTest extends TestCase
{
  private ViacaoRepository&MockObject    $repo;
  private HistoricoRepository&MockObject $historico;
  private ViacaoService                  $service;

  protected function setUp(): void
  {
    $this->repo      = $this->createMock(ViacaoRepository::class);
    $this->historico = $this->createMock(HistoricoRepository::class);

    // Validator real
    $this->service = new ViacaoService(
      $this->repo,
      new ViacaoValidator(),
      $this->historico
    );
  }

  // ── create() ────────────────────────────────────────────

  public function testCreateLancaExcecaoSeDadosInvalidos(): void
  {
    $this->expectException(\Exception::class);

    $this->service->create(['nome' => '', 'url' => '', 'cidade' => '']);
  }

  public function testCreateChamaRepositoryComDadosCorretos(): void
  {
    $this->repo
      ->expects($this->once())
      ->method('create')
      ->with($this->callback(function (array $data) {
        return $data['nome']   === 'Viação Cometa'
          && $data['url']    === 'https://cometa.com.br'
          && $data['cidade'] === 'São Paulo'
          && $data['status'] === 'ativo'
          && $data['logo']   === null;
      }))
      ->willReturn(1);

    $this->historico->expects($this->once())->method('log');

    $id = $this->service->create([
      'nome'   => 'Viação Cometa',
      'url'    => 'https://cometa.com.br',
      'cidade' => 'São Paulo',
      'status' => 'ativo',
    ]);

    $this->assertSame(1, $id);
  }

  // ── update() ────────────────────────────────────────────

  public function testUpdateLancaExcecaoSeViacaoNaoExiste(): void
  {
    $this->repo->method('find')->willReturn(null);
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Viação não encontrada.');

    $this->service->update(999, [
      'nome'   => 'X',
      'url'    => 'https://x.com',
      'cidade' => 'X',
    ]);
  }

  public function testUpdateRegistraHistoricoSoSeHouverMudanca(): void
  {
    $viacaoAntiga = Viacao::fromRow([
      'id' => 1, 'nome' => 'Cometa', 'url' => 'https://cometa.com',
      'cidade' => 'SP', 'status' => 'ativo', 'logo' => null,
      'criado_em' => null, 'alterado_em' => null,
    ]);

    $this->repo->method('find')->willReturn($viacaoAntiga);
    $this->repo->expects($this->once())->method('update');

    // Nenhum campo mudou — histórico não deve ser chamado
    $this->historico->expects($this->never())->method('log');

    $this->service->update(1, [
      'nome'   => 'Cometa',
      'url'    => 'https://cometa.com',
      'cidade' => 'SP',
      'status' => 'ativo',
    ]);
  }

  // ── delete() ────────────────────────────────────────────

  public function testDeleteNaoFazNadaSeViacaoNaoExiste(): void
  {
    $this->repo->method('find')->willReturn(null);
    $this->repo->expects($this->never())->method('delete');

    $this->service->delete(999);
  }

  public function testDeleteChamaRepositoryERegistraHistorico(): void
  {
    $viacao = Viacao::fromRow([
      'id' => 1, 'nome' => 'Cometa', 'url' => 'https://cometa.com',
      'cidade' => 'SP', 'status' => 'ativo', 'logo' => null,
      'criado_em' => null, 'alterado_em' => null,
    ]);

    $this->repo->method('find')->willReturn($viacao);
    $this->repo->expects($this->once())->method('delete')->with(1);
    $this->historico->expects($this->once())->method('log');

    $this->service->delete(1);
  }
}
