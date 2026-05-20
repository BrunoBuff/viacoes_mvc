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

  <header class="page-header">
    <div>
      <h1 class="page-title">Cadastrar Usuário</h1>
      <p class="page-subtitle">
        Preencha os dados para criar um novo usuário
      </p>
    </div>
  </header>

  <section class="header-actions">
    <div class="header-buttons">
      <a href="/admin/usuarios" class="btn btn-secondary">
        Voltar para a lista
      </a>
    </div>
  </section>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
      <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="form-card">
    <form method="POST" action="/admin/usuarios">

      <div class="campo">
        <label for="nome">Nome</label>
        <input
          type="text"
          id="nome"
          name="nome"
          value="<?= htmlspecialchars($old['nome'] ?? '') ?>"
          required
        >
      </div>

      <div class="campo">
        <label for="email">E-mail</label>
        <input
          type="email"
          id="email"
          name="email"
          value="<?= htmlspecialchars($old['email'] ?? '') ?>"
          required
        >
      </div>

      <div class="campo">
        <label for="password">Senha</label>
        <input
          type="password"
          id="password"
          name="password"
          required
        >
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          Salvar Usuário
        </button>

        <a href="/admin/usuarios" class="btn btn-secondary">
          Cancelar
        </a>
      </div>

    </form>
  </div>

</div>
<script src="/script.js"></script>
</body>
</html>
