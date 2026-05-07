<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? htmlspecialchars($title) . ' | Quero Passagem' : 'Quero Passagem - Passagens de Ônibus' ?></title>

  <link rel="stylesheet" href="/assets/css/style.css">

  <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body>

<header class="header">
  <div class="container header-content">
    <div class="logo">
      <a href="/">
        <img src="/assets/img/logo.png" alt="Quero Passagem">
      </a>
    </div>

    <nav class="menu">
      <a href="/" class="menu-item active">Passagens</a>
      <a href="/hoteis" class="menu-item">Hotéis <span class="novo">Novo</span></a>
    </nav>

    <div class="actions">
      <a href="/ajuda" class="help">Central de Ajuda</a>
      <button class="btn-login">Entrar</button>
    </div>
  </div>
</header>

<main class="main-content">
  <div class="container">
    <?= $content ?? 'Conteúdo não encontrado.' ?>
  </div>
</main>

<footer class="footer">
  <div class="container footer-grid">
    <div class="footer-info">
      <h4>Quero Passagem</h4>
      <p>Sua viagem de ônibus começa aqui.</p>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <p>Copyright <?= date('Y') ?> © QueroPassagem.com.br - Todos os direitos reservados</p>
    </div>
  </div>
</footer>

<script src="/assets/js/script.js"></script>

<?php if (isset($extra_js)) echo $extra_js; ?>

</body>
</html>
