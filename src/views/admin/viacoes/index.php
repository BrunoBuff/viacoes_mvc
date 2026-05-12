<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista de Viações</title>
  <link rel="stylesheet" href="/styles.css">
</head>
<body>

<h1>Lista de Viações</h1>

<?php
// __DIR__ = src/views/admin/viacoes
// dirname(__DIR__, 4) = raiz do projeto
$uploadBase = dirname(__DIR__, 4) . '/src/public/uploads/logos/';

$flash = \App\Core\View::pullFlash();
if ($flash): ?>
  <div class="alert-<?= $flash['type'] === 'success' ? 'sucesso' : 'erro' ?>">
    <p><?= htmlspecialchars($flash['message']) ?></p>
  </div>
<?php endif; ?>

<div class="header-actions">
  <a href="/admin/viacoes/create" class="btn-novo">+ Cadastrar nova viação</a>
  <a href="/admin/historico" class="btn-novo btn-historico-nav">Ver Histórico</a>

  <form method="GET" action="/admin/viacoes" class="form-busca">
    <input type="text" name="nome"
           value="<?= htmlspecialchars($filtros['busca']) ?>"
           placeholder="Buscar por nome...">
    <button type="submit">Buscar</button>
    <?php if ($filtros['busca'] !== ''): ?>
      <a href="/admin/viacoes">Limpar</a>
    <?php endif; ?>
  </form>

  <a href="/logout">Sair</a>
</div>

<table>
  <thead>
  <tr>
    <th>ID</th>
    <th>Viação</th>
    <th>URL</th>
    <th>Cidade</th>
    <th>Status</th>
    <th>Ações</th>
  </tr>
  </thead>
  <tbody>
  <?php if (empty($viacoes)): ?>
    <tr><td colspan="6" class="empty-row">Nenhuma viação cadastrada.</td></tr>
  <?php else: ?>
    <?php foreach ($viacoes as $v): ?>
      <tr>
        <td><?= $v->id ?></td>
        <td>
          <div class="viacao-info">
            <?php if (!empty($v->logo) && file_exists($uploadBase . $v->logo)): ?>
              <img src="/uploads/logos/<?= htmlspecialchars($v->logo) ?>"
                   class="logo-avatar" alt="Logo">
            <?php else: ?>
              <div class="logo-placeholder">
                <?= strtoupper(mb_substr($v->nome, 0, 1, 'UTF-8')) ?>
              </div>
            <?php endif; ?>
            <strong><?= htmlspecialchars($v->nome) ?></strong>
          </div>
        </td>
        <td>
          <a href="<?= htmlspecialchars($v->url) ?>" target="_blank" class="url-link">
            Visitar site
          </a>
        </td>
        <td><?= htmlspecialchars($v->cidade ?: '—') ?></td>
        <td class="status-<?= $v->status ?>">
          <?= ucfirst($v->status) ?>
        </td>
        <td>
          <a href="/admin/viacoes/<?= $v->id ?>/edit" class="btn-edit">Editar</a>
          <form method="POST" action="/admin/viacoes/<?= $v->id ?>"
                style="display:inline"
                onsubmit="return confirm('Tem certeza que deseja excluir esta viação?')">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn-delete-inline">Excluir</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
  </tbody>
</table>

</body>
</html>
