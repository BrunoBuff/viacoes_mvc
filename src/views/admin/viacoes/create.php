<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Viação</title>
  <link rel="stylesheet" href="/styles.css">
</head>
<body>

<h1>Cadastrar Viação</h1>

<?php if (!empty($errors)): ?>
  <div class="alert-erro">
    <?php foreach ($errors as $e): ?>
      <p>⚠ <?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="POST" action="/admin/viacoes" enctype="multipart/form-data">

  <div class="campo">
    <label>Nome *</label>
    <input type="text" name="nome"
           value="<?= htmlspecialchars($old['nome'] ?? '') ?>"
           placeholder="Ex: Viação Cometa" required>
  </div>

  <div class="campo">
    <label>URL *</label>
    <input type="url" name="url" id="url"
           value="<?= htmlspecialchars($old['url'] ?? '') ?>"
           placeholder="https://exemplo.com.br" required>
    <span id="url-error" class="campo-erro"></span>
  </div>

  <div class="campo">
    <label>Cidade *</label>
    <input type="text" name="cidade"
           value="<?= htmlspecialchars($old['cidade'] ?? '') ?>"
           placeholder="Ex: São Paulo">
  </div>

  <div class="campo">
    <label>Status</label>
    <select name="status">
      <option value="ativo"
        <?= (($old['status'] ?? 'ativo') === 'ativo') ? 'selected' : '' ?>>Ativo</option>
      <option value="inativo"
        <?= (($old['status'] ?? '') === 'inativo') ? 'selected' : '' ?>>Inativo</option>
    </select>
  </div>

  <div class="campo">
    <label>Logo (opcional — jpg, png, webp · máx 2 MB)</label>
    <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp">
  </div>

  <button type="submit">Cadastrar</button>

</form>

<br>
<a href="/admin/viacoes">← Voltar para a lista</a>

<script src="/assets/js/script.js"></script>
</body>
</html>
