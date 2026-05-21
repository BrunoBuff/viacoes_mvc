<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuário</title>
  <link rel="stylesheet" href="/styles.css">
</head>

<body class="admin-page">
<div class="page-container">

  <!-- HEADER -->
  <header class="page-header">
    <div>
      <h1 class="page-title">Editar Usuário</h1>
      <p class="page-subtitle">
        Alterando dados de <strong><?= htmlspecialchars($usuario->nome) ?></strong>
      </p>
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
    <form method="POST" action="/admin/usuarios/<?= (int) $usuario->id ?>">

      <input type="hidden" name="_method" value="PUT">

      <div class="form-grid">

        <div class="campo">
          <label for="nome">Nome *</label>
          <input
            type="text"
            id="nome"
            name="nome"
            value="<?= htmlspecialchars($old['nome'] ?? $usuario->nome) ?>"
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
            value="<?= htmlspecialchars($old['email'] ?? $usuario->email) ?>"
            required
          >
        </div>

        <div class="campo">
          <label for="password">Nova Senha
            <span class="label-helper">(deixe em branco para não alterar)</span>
          </label>
          <input
            type="password"
            id="password"
            name="password"
            minlength="6"
            placeholder="Mínimo 6 caracteres"
          >
        </div>

        <div class="campo">
          <label for="password_confirm">Confirmar Nova Senha</label>
          <input
            type="password"
            id="password_confirm"
            name="password_confirm"
            minlength="6"
            placeholder="Repita a nova senha"
          >
          <span id="senha-erro" class="campo-erro"></span>
        </div>

      </div>

      <div class="form-actions">
        <a href="/admin/usuarios" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
      </div>

    </form>
  </div>

</div>

<script src="/script.js"></script>
<script>
  // Validação client-side de confirmação de senha (opcional na edição)
  (function () {
    const senha    = document.getElementById('password');
    const confirma = document.getElementById('password_confirm');
    const erro     = document.getElementById('senha-erro');

    function checar() {
      if (senha.value === '' && confirma.value === '') {
        erro.textContent = '';
        confirma.setCustomValidity('');
        return;
      }
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
