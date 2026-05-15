<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Login — Viações</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/login.css">

</head>
<body>

<div class="painel-esquerdo">

  <img src="/imagem/logo-branca.png" alt="Logo Viações" class="quero">

</div>

<div class="painel-direito">

  <div class="topo">

    <a href="#" class="link-ajuda">

      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="9"></circle>
        <path d="M9.09 9a3 3 0 1 1 5.82 1c0 2-3 2-3 4"></path>
        <line x1="12" y1="17" x2="12.01" y2="17"></line>
      </svg>

      Central de ajuda

    </a>

  </div>

  <div class="login-box">

    <h1>Acesse suas viagens</h1>

    <p class="subtitulo">
      Digite seu e-mail ou número de celular para continuar
    </p>

    <?php if (!empty($erro)): ?>
      <div class="alert-erro">
        <?= htmlspecialchars($erro) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="/login">

      <div class="campo">
        <input
          type="email"
          name="email"
          placeholder="E-mail ou número de celular"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          required
        >
        <input
          type="password"
          name="password"
          placeholder="Senha"
          required
        >
      </div>

      <div class="info-box">

        <svg fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="16" x2="12" y2="12"></line>
          <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>

        <p>
          Se você é o Administrador, utilize o mesmo e-mail ou número
          de celular.
        </p>

      </div>

      <button type="submit" class="btn-continuar">
        CONTINUAR
      </button>

    </form>

    <div class="divisor">
      <span>ou</span>
    </div>

    <a href="#" class="btn-social">
      <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/facebook/facebook-original.svg">
      Continue com o Facebook
    </a>

    <a href="#" class="btn-social">
      <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg">
      Continue com o Google
    </a>

    <a href="#" class="btn-social">

      <svg width="18" height="18" viewBox="0 0 24 24" fill="black">
        <path d="M16.365 1.43c0 1.14-.41 2.006-1.23 2.84-.82.833-1.81 1.31-2.97 1.216-.07-1.11.44-2.08 1.22-2.87.78-.79 1.86-1.28 2.98-1.186z"/>
      </svg>

      Continue com a Apple

    </a>

  </div>

</div>

</body>
</html>
