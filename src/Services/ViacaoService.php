<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Viacao;
use App\Repositories\ViacaoRepository;
use App\Repositories\HistoricoRepository;
use App\Validators\ViacaoValidator;
use Exception;

final class ViacaoService
{
  private ViacaoRepository    $repo;
  private ViacaoValidator     $validator;
  private HistoricoRepository $historico;
  // __DIR__ = src/Services → dirname(__DIR__, 2) = raiz do projeto
  private string $uploadDir;

  public function __construct(
    ?ViacaoRepository    $repo      = null,
    ?ViacaoValidator     $validator = null,
    ?HistoricoRepository $historico = null
  ) {
    $this->repo      = $repo      ?? new ViacaoRepository();
    $this->validator = $validator ?? new ViacaoValidator();
    $this->historico = $historico ?? new HistoricoRepository();
    $this->uploadDir = dirname(__DIR__, 2) . '/src/public/uploads/logos/';
  }

  public function all(string $busca, string $status, string $ordem, string $dir): array
  {
    $isHomeQuery = ($busca === '' && $status === 'ativo' && $ordem === 'nome' && $dir === 'ASC');

    if ($isHomeQuery) {
      $cached = \getCachedData('viacoes');
      if ($cached !== null) {
        return array_map(fn($row) => Viacao::fromRow($row), $cached);
      }
    }

    $viacoes = $this->repo->all($busca, $status, $ordem, $dir);

    if ($isHomeQuery) {
      \setCachedData('viacoes_ativas', array_map(fn($v) => (array) $v, $viacoes));
    }

    return $viacoes;
  }

  public function find(int $id): ?Viacao
  {
    return $this->repo->find($id);
  }

  public function create(array $data, ?array $fileLogo = null): int
  {
    $errors = $this->validator->validate($data);
    if ($errors !== []) throw new Exception(implode('|', $errors));

    $nome = trim($data['nome']);
    $id   = $this->repo->create([
      'nome'   => $nome,
      'url'    => trim($data['url']),
      'cidade' => trim($data['cidade']),
      'status' => ($data['status'] ?? '') === 'inativo' ? 'inativo' : 'ativo',
      'logo'   => $this->handleUpload($fileLogo),
    ]);

    $this->historico->log($id, 'Criado', "Viação '{$nome}' cadastrada.", $nome);
    \invalidateCache('viacoes_ativas');

    return $id;
  }

  public function update(int $id, array $data, ?array $fileLogo = null): void
  {
    $old = $this->repo->find($id);
    if (!$old) throw new Exception('Viação não encontrada.');

    $errors = $this->validator->validate($data);
    if ($errors !== []) throw new Exception(implode('|', $errors));

    $updateData = [
      'nome'   => trim($data['nome']),
      'url'    => trim($data['url']),
      'cidade' => trim($data['cidade']),
      'status' => ($data['status'] ?? '') === 'inativo' ? 'inativo' : 'ativo',
    ];

    $mudancas = [];
    if ($old->nome   !== $updateData['nome'])   $mudancas[] = "Nome: '{$old->nome}' → '{$updateData['nome']}'";
    if ($old->url    !== $updateData['url'])    $mudancas[] = "URL: '{$old->url}' → '{$updateData['url']}'";
    if ($old->cidade !== $updateData['cidade']) $mudancas[] = "Cidade: '{$old->cidade}' → '{$updateData['cidade']}'";
    if ($old->status !== $updateData['status']) $mudancas[] = "Status: '{$old->status}' → '{$updateData['status']}'";

    if ($fileLogo !== null && $fileLogo['error'] === UPLOAD_ERR_OK) {
      $updateData['logo'] = $this->handleUpload($fileLogo);
      $mudancas[] = 'Logo atualizada';
    }

    $this->repo->update($id, $updateData);

    if (!empty($mudancas)) {
      $this->historico->log($id, 'Editado', implode(' | ', $mudancas), $updateData['nome']);
    }

    \invalidateCache('viacoes_ativas');
  }

  public function delete(int $id): void
  {
    $viacao = $this->repo->find($id);
    if (!$viacao) return;

    $this->repo->delete($id);
    $this->historico->log($id, 'Excluido', "Viação '{$viacao->nome}' foi excluída.", $viacao->nome);

    if ($viacao->logo) {
      $path = $this->uploadDir . $viacao->logo;
      if (file_exists($path)) unlink($path);
    }

    \invalidateCache('viacoes_ativas');
  }

  private function handleUpload(?array $file): ?string
  {
    if ($file === null || $file['error'] !== UPLOAD_ERR_OK) return null;

    $tmpName = $file['tmp_name'];

    if (!is_uploaded_file($tmpName)) {
      throw new Exception('Arquivo de upload inválido.');
    }

    $allowedMimes = [
      'image/jpeg' => 'jpg',
      'image/png'  => 'png',
      'image/webp' => 'webp',
    ];

    $mime = mime_content_type($tmpName);
    if (!array_key_exists($mime, $allowedMimes)) {
      throw new Exception('Apenas imagens JPG, PNG ou WEBP são permitidas.');
    }

    $nomeLogo = uniqid('logo_') . '.' . $allowedMimes[$mime];

    if (!is_dir($this->uploadDir)) {
      mkdir($this->uploadDir, 0755, true);
    }

    if (!move_uploaded_file($tmpName, $this->uploadDir . $nomeLogo)) {
      throw new Exception('Falha ao mover a imagem para o diretório.');
    }

    return $nomeLogo;
  }
}
