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
  private ViacaoRepository $repo;
  private ViacaoValidator $validator;
  private HistoricoRepository $historico;
  private string $uploadDir;

  public function __construct(
    ?ViacaoRepository $repo = null,
    ?ViacaoValidator $validator = null,
    ?HistoricoRepository $historico = null
  ) {
    $this->repo = $repo ?? new ViacaoRepository();
    $this->validator = $validator ?? new ViacaoValidator();
    $this->historico = $historico ?? new HistoricoRepository();

    // raiz/src/public/uploads/logos/
    $this->uploadDir = dirname(__DIR__, 2) . '/src/public/uploads/logos/';
  }

  public function all(string $busca, string $status, string $ordem, string $dir): array
  {
    $isHomeQuery = (
      $busca === '' &&
      $status === 'ativo' &&
      $ordem === 'nome' &&
      $dir === 'ASC'
    );

    if ($isHomeQuery) {
      $cached = \getCachedData('viacoes_ativas');

      if ($cached !== null) {
        return array_map(
          fn(array $row) => Viacao::fromRow($row),
          $cached
        );
      }
    }

    $viacoes = $this->repo->all($busca, $status, $ordem, $dir);

    if ($isHomeQuery) {
      \setCachedData(
        'viacoes_ativas',
        array_map(fn($v) => (array) $v, $viacoes)
      );
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

    if ($errors !== []) {
      throw new Exception(implode('|', $errors));
    }

    $nome = trim($data['nome']);

    // 1. Salva o registro principal no banco de dados
    $id = $this->repo->create([
      'nome'   => $nome,
      'url'    => trim($data['url']),
      'cidade' => trim($data['cidade']),
      'status' => ($data['status'] ?? '') === 'inativo' ? 'inativo' : 'ativo',
      'logo'   => $this->handleUpload($fileLogo),
    ]);

    // 2. Busca o ID do administrador logado na sessão do sistema
    $userId = $_SESSION['user_id'] ?? $_SESSION['auth']['id'] ?? 1;

    // 3. Grava o histórico no formato correto esperado pelo HistoricoRepository
    $this->historico->log(
      $id,        // viacao_id
      $userId,    // user_id
      'CREATE',   // acao
      null,       // antes (não existia estado anterior no cadastro)
      $data       // depois (dados que foram inseridos)
    );

    \invalidateCache('viacoes_ativas');

    return $id;
  }

  public function update(int $id, array $data, ?array $fileLogo = null): void
  {
    $old = $this->repo->find($id);

    if (!$old) {
      throw new Exception('Viação não encontrada.');
    }

    $errors = $this->validator->validate($data);

    if ($errors !== []) {
      throw new Exception(implode('|', $errors));
    }

    $updateData = [
      'nome'   => trim($data['nome']),
      'url'    => trim($data['url']),
      'cidade' => trim($data['cidade']),
      'status' => ($data['status'] ?? '') === 'inativo' ? 'inativo' : 'ativo',
    ];

    $mudancas = [];

    if ($old->nome !== $updateData['nome']) {
      $mudancas[] = "Nome: '{$old->nome}' → '{$updateData['nome']}'";
    }

    if ($old->url !== $updateData['url']) {
      $mudancas[] = "URL: '{$old->url}' → '{$updateData['url']}'";
    }

    if ($old->cidade !== $updateData['cidade']) {
      $mudancas[] = "Cidade: '{$old->cidade}' → '{$updateData['cidade']}'";
    }

    if ($old->status !== $updateData['status']) {
      $mudancas[] = "Status: '{$old->status}' → '{$updateData['status']}'";
    }

    if ($fileLogo !== null && $fileLogo['error'] === UPLOAD_ERR_OK) {
      $updateData['logo'] = $this->handleUpload($fileLogo);
      $mudancas[] = 'Logo atualizada';
    }

    // 1. Atualiza os dados no banco
    $this->repo->update($id, $updateData);

    // 2. Registra a auditoria caso tenha ocorrido alguma mudança real
    if (!empty($mudancas)) {
      $userId = $_SESSION['user_id'] ?? $_SESSION['auth']['id'] ?? 1;

      $this->historico->log(
        $id,
        $userId,
        'UPDATE',
        [
          'nome'   => $old->nome,
          'url'    => $old->url,
          'cidade' => $old->cidade,
          'status' => $old->status,
          'logo'   => $old->logo
        ],
        $updateData
      );
    }

    \invalidateCache('viacoes_ativas');
  }

  public function delete(int $id): void
  {
    $viacao = $this->repo->find($id);

    if (!$viacao) {
      return;
    }

    $userId = $_SESSION['user_id'] ?? $_SESSION['auth']['id'] ?? 1;

    // 1. Grava a auditoria de exclusão ANTES de apagar a viação do banco (Garante a integridade da Foreign Key)
    $this->historico->log(
      $id,
      $userId,
      'DELETE',
      [
        'nome'   => $viacao->nome,
        'url'    => $viacao->url,
        'cidade' => $viacao->cidade,
        'status' => $viacao->status,
        'logo'   => $viacao->logo
      ],
      null
    );

    // 2. Agora sim, remove com segurança do banco de dados
    $this->repo->delete($id);

    // 3. Remove o arquivo físico de imagem se ele existir
    if ($viacao->logo) {
      $path = $this->uploadDir . $viacao->logo;

      if (file_exists($path)) {
        unlink($path);
      }
    }

    \invalidateCache('viacoes_ativas');
  }

  private function handleUpload(?array $file): ?string
  {
    if (
      $file === null ||
      $file['error'] !== UPLOAD_ERR_OK
    ) {
      return null;
    }

    $tmpName = $file['tmp_name'];

    if (!is_uploaded_file($tmpName)) {
      throw new Exception('Arquivo de upload inválido.');
    }

    $maxSize = 2 * 1024 * 1024;

    if ($file['size'] > $maxSize) {
      throw new Exception('Arquivo muito grande. Máximo permitido: 2MB.');
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
      throw new Exception('Extensão de arquivo inválida.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpName);
    finfo_close($finfo);

    $allowedMimes = [
      'image/jpeg' => 'jpg',
      'image/png'  => 'png',
      'image/webp' => 'webp',
    ];

    if (!array_key_exists($mime, $allowedMimes)) {
      throw new Exception('Apenas imagens JPG, PNG ou WEBP são permitidas.');
    }

    $nomeLogo = bin2hex(random_bytes(16)) . '.' . $allowedMimes[$mime];

    if (!is_dir($this->uploadDir)) {
      mkdir($this->uploadDir, 0755, true);
    }

    if (!move_uploaded_file($tmpName, $this->uploadDir . $nomeLogo)) {
      throw new Exception('Falha ao salvar a imagem.');
    }

    return $nomeLogo;
  }
}
