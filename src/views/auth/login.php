<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Viações</title>
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body class="pagina-login">

<div class="login-box">
  <h1>Acesso ao Sistema</h1>

  <?php if (!empty($erro)): ?>
    <div class="alert-erro"><p>⚠ <?= htmlspecialchars($erro) ?></p></div>
  <?php endif; ?>

  <form method="POST" action="/login">
    <div class="campo">
      <label>E-mail</label>
      <input type="email" name="email"
             placeholder="admin@exemplo.com"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
             required>
    </div>
    <div class="campo">
      <label>Senha</label>
      <input type="password" name="senha"
             placeholder="••••••••"
             required>
    </div>
    <button type="submit">Entrar</button>
  </form>
</div>

</body>
</html>
