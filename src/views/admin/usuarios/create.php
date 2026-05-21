<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Usuário</title>
  <link rel="stylesheet" href="/styles.css">
</head>

<body class="admin-page">
<div class="page-container">

  <!-- HEADER -->
  <header class="page-header">
    <div>
      <h1 class="page-title">Cadastrar Usuário</h1>
      <p class="page-subtitle">Preencha os dados para criar um novo acesso ao sistema</p>
    </div>
  </header>

  <!-- BOTÃO VOLTAR -->
  <section class="header-actions">
    <div class="header-buttons">
      <a href="/admin/usuarios" class="btn btn-secondary">← Voltar para a lista</a>
    </div>
  </section>

  <!-- ERROS DE VALIDAÇÃO -->
  <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
      <?php foreach ($errors as $error): ?>
        <p>⚠ <?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- FORMULÁRIO -->
  <div class="form-card">
    <form method="POST" action="/admin/usuarios">

      <div class="form-grid">

        <div class="campo">
          <label for="nome">Nome *</label>
          <input
            type="text"
            id="nome"
            name="nome"
            value="<?= htmlspecialchars($old['nome'] ?? '') ?>"
            placeholder="Ex: Maria Silva"
            required
            minlength="3"
          >
        </div>

        <div class="campo">
          <label for="email">E-mail *</label>
          <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
            placeholder="Ex: maria@empresa.com"
            required
          >
        </div>

        <div class="campo">
          <label for="password">Senha *
            <span class="label-helper">(mínimo 6 caracteres)</span>
          </label>
          <input
            type="password"
            id="password"
            name="password"
            required
            minlength="6"
          >
        </div>

        <!-- CORREÇÃO: campo de confirmação de senha ausente na versão original -->
        <div class="campo">
          <label for="password_confirm">Confirmar Senha *</label>
          <input
            type="password"
            id="password_confirm"
            name="password_confirm"
            required
            minlength="6"
          >
          <span id="senha-erro" class="campo-erro"></span>
        </div>

      </div>

      <div class="form-actions">
        <a href="/admin/usuarios" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Cadastrar Usuário</button>
      </div>

    </form>
  </div>

</div>

<script src="/script.js"></script>
<script>
  // Validação client-side de confirmação de senha
  (function () {
    const senha    = document.getElementById('password');
    const confirma = document.getElementById('password_confirm');
    const erro     = document.getElementById('senha-erro');

    function checar() {
      if (confirma.value === '') { erro.textContent = ''; return; }
      if (senha.value !== confirma.value) {
        erro.textContent = 'As senhas não coincidem.';
        confirma.setCustomValidity('As senhas não coincidem.');
      } else {
        erro.textContent = '';
        confirma.setCustomValidity('');
      }
    }

    senha.addEventListener('input', checar);
    confirma.addEventListener('input', checar);
  })();
</script>
</body>
</html>
