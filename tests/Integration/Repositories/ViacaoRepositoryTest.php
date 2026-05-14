<?php
declare(strict_types=1);
namespace Tests\Integration\Repositories;

use PHPUnit\Framework\TestCase;
use App\Repositories\ViacaoRepository;
use App\Models\Viacao;
use Tests\Support\DatabaseFactory;

class ViacaoRepositoryTest extends TestCase
{
  private ViacaoRepository $repo;

  protected function setUp(): void
  {
    // Banco limpo a cada teste
    $pdo        = DatabaseFactory::make();
    $this->repo = new ViacaoRepository($pdo);
  }

  public function testCreateEFindRetornamMesmoRegistro(): void
  {
    $id = $this->repo->create([
      'nome'   => 'Viação Teste',
      'url'    => 'https://teste.com',
      'cidade' => 'Curitiba',
      'status' => 'ativo',
      'logo'   => null,
    ]);

    $viacao = $this->repo->find($id);

    $this->assertInstanceOf(Viacao::class, $viacao);
    $this->assertSame('Viação Teste', $viacao->nome);
    $this->assertSame('Curitiba', $viacao->cidade);
  }

  public function testFindRetornaNullParaIdInexistente(): void
  {
    $this->assertNull($this->repo->find(9999));
  }

  public function testAllFiltraPorBusca(): void
  {
    $this->repo->create(['nome' => 'Viação Cometa',  'url' => 'https://a.com', 'cidade' => 'SP', 'status' => 'ativo', 'logo' => null]);
    $this->repo->create(['nome' => 'Viação Itapemirim', 'url' => 'https://b.com', 'cidade' => 'RJ', 'status' => 'ativo', 'logo' => null]);

    $resultado = $this->repo->all('Cometa', '', 'nome', 'ASC');

    $this->assertCount(1, $resultado);
    $this->assertSame('Viação Cometa', $resultado[0]->nome);
  }

  public function testUpdateAlteraDadosCorretamente(): void
  {
    $id = $this->repo->create(['nome' => 'Antiga', 'url' => 'https://antiga.com', 'cidade' => 'BH', 'status' => 'ativo', 'logo' => null]);

    $this->repo->update($id, ['nome' => 'Nova', 'url' => 'https://nova.com', 'cidade' => 'BH', 'status' => 'inativo']);

    $viacao = $this->repo->find($id);
    $this->assertSame('Nova', $viacao->nome);
    $this->assertSame('inativo', $viacao->status);
  }

  public function testDeleteRemoveRegistro(): void
  {
    $id = $this->repo->create(['nome' => 'Para Deletar', 'url' => 'https://x.com', 'cidade' => 'X', 'status' => 'ativo', 'logo' => null]);

    $this->repo->delete($id);

    $this->assertNull($this->repo->find($id));
  }
}
