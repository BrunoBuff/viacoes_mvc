<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Viação</title>
  <link rel="stylesheet" href="/styles.css">
</head>

<body class="admin-page">

<div class="page-container">

  <!-- =========================================================
       HEADER DA PÁGINA
  ========================================================= -->
  <div class="page-header">

    <div>
      <h1 class="page-title">
        Cadastrar Viação
      </h1>

      <p class="page-subtitle">
        Preencha os dados para adicionar uma nova viação
      </p>
    </div>

  </div>


  <!-- =========================================================
       ALERTAS / ERROS DE VALIDAÇÃO
  ========================================================= -->
  <?php if (!empty($errors)): ?>

    <div class="alert alert-error">

      <?php foreach ($errors as $e): ?>
        <p>⚠ <?= htmlspecialchars($e) ?></p>
      <?php endforeach; ?>

    </div>

  <?php endif; ?>


  <!-- =========================================================
       CARD DO FORMULÁRIO
  ========================================================= -->
  <div class="form-card">

    <form method="POST" action="/admin/viacoes" enctype="multipart/form-data">


      <!-- =========================================================
           GRID DOS CAMPOS PRINCIPAIS
      ========================================================= -->
      <div class="form-grid">


        <!-- ===================== NOME ===================== -->
        <div class="campo">

          <label>
            Nome *
          </label>

          <input
            type="text"
            name="nome"
            value="<?= htmlspecialchars($old['nome'] ?? '') ?>"
            placeholder="Ex: Viação Cometa"
            required
          >

        </div>


        <!-- ====================== URL ===================== -->
        <div class="campo">

          <label>
            URL *
          </label>

          <input
            type="url"
            name="url"
            id="url"
            value="<?= htmlspecialchars($old['url'] ?? '') ?>"
            placeholder="https://exemplo.com.br"
            required
          >

          <!-- Erro de validação JS -->
          <span id="url-error" class="campo-erro"></span>

        </div>


        <!-- ==================== CIDADE ==================== -->
        <div class="campo">

          <label>
            Cidade *
          </label>

          <input
            type="text"
            name="cidade"
            value="<?= htmlspecialchars($old['cidade'] ?? '') ?>"
            placeholder="Ex: São Paulo"
          >

        </div>


        <!-- ==================== STATUS ==================== -->
        <div class="campo">

          <label>
            Status
          </label>

          <select name="status">

            <option
              value="ativo"
              <?= (($old['status'] ?? 'ativo') === 'ativo') ? 'selected' : '' ?>
            >
              Ativo
            </option>

            <option
              value="inativo"
              <?= (($old['status'] ?? '') === 'inativo') ? 'selected' : '' ?>
            >
              Inativo
            </option>

          </select>

        </div>

      </div>


      <!-- =========================================================
           CAMPO DE UPLOAD DA LOGO
      ========================================================= -->
      <div class="campo">

        <label>
          Logo

          <span class="label-helper">
            (opcional — jpg, png, webp · máx 2 MB)
          </span>
        </label>

        <input
          type="file"
          name="logo"
          accept=".jpg,.jpeg,.png,.webp"
        >

      </div>


      <!-- =========================================================
           AÇÕES DO FORMULÁRIO
      ========================================================= -->
      <div class="form-actions">

        <!-- BOTÃO VOLTAR -->
        <a href="/admin/viacoes" class="btn btn-secondary">
          Voltar
        </a>

        <!-- BOTÃO CADASTRAR -->
        <button type="submit" class="btn btn-primary">
          Cadastrar
        </button>

      </div>

    </form>

  </div>

</div>

<script src="/script.js"></script>
</body>
</html>
