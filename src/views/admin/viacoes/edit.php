<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Viação</title>
  <link rel="stylesheet" href="/styles.css">
</head>
<body>

<h1>Editar Viação</h1>

<?php if (!empty($errors)): ?>
  <div class="alert-erro">
    <?php foreach ($errors as $e): ?>
      <p>⚠ <?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php
// __DIR__ = src/views/admin/viacoes
// dirname(__DIR__, 4) = raiz do projeto
$uploadBase = dirname(__DIR__, 4) . '/src/public/uploads/logos/';
?>

<form method="POST" action="/admin/viacoes/<?= $viacao->id ?>" enctype="multipart/form-data">
  <input type="hidden" name="_method" value="PUT">

  <div class="campo">
    <label>Nome *</label>
    <input type="text" name="nome"
           value="<?= htmlspecialchars($old['nome'] ?? '') ?>" required>
  </div>

  <div class="campo">
    <label>URL *</label>
    <input type="url" name="url" id="url"
           value="<?= htmlspecialchars($old['url'] ?? '') ?>" required>
    <span id="url-error" class="campo-erro"></span>
  </div>

  <div class="campo">
    <label>Cidade *</label>
    <input type="text" name="cidade"
           value="<?= htmlspecialchars($old['cidade'] ?? '') ?>">
  </div>

  <div class="campo">
    <label>Status</label>
    <select name="status">
      <option value="ativo"
        <?= (($old['status'] ?? '') === 'ativo') ? 'selected' : '' ?>>Ativo</option>
      <option value="inativo"
        <?= (($old['status'] ?? '') === 'inativo') ? 'selected' : '' ?>>Inativo</option>
    </select>
  </div>

  <div class="campo">
    <label>Trocar Logo (opcional — manter vazio para não alterar)</label>
    <?php if (!empty($viacao->logo) && file_exists($uploadBase . $viacao->logo)): ?>
      <div style="margin-bottom:8px">
        <img src="/uploads/logos/<?= htmlspecialchars($viacao->logo) ?>"
             style="height:60px;border-radius:6px;border:1px solid #ddd" alt="Logo atual">
        <small style="display:block;color:#666;margin-top:4px">Logo atual</small>
      </div>
    <?php endif; ?>
    <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp">
  </div>

  <button type="submit">Salvar Alterações</button>

</form>

<br>
<a href="/admin/viacoes">← Voltar para a lista</a>

<script src="/assets/js/script.js"></script>
</body>
</html>
