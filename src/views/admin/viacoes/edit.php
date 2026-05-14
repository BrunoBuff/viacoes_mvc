<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Viação</title>
  <link rel="stylesheet" href="/styles.css">
</head>

<body class="admin-page">

<!-- =========================================================
     CONTAINER PRINCIPAL DA PÁGINA
========================================================= -->
<div class="page-container">


  <!-- =========================================================
       HEADER DA PÁGINA
  ========================================================= -->
  <div class="page-header">

    <div>
      <h1 class="page-title">
        Editar Viação
      </h1>

      <p class="page-subtitle">
        Atualize as informações da viação cadastrada
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
       DIRETÓRIO BASE DOS UPLOADS
  ========================================================= -->
  <?php
  $uploadBase = dirname(__DIR__, 4) . '/src/public/uploads/logos/';
  ?>


  <!-- =========================================================
       CARD DO FORMULÁRIO
  ========================================================= -->
  <div class="form-card">


    <!-- =========================================================
         FORMULÁRIO DE EDIÇÃO
    ========================================================= -->
    <form
      method="POST"
      action="/admin/viacoes/<?= $viacao->id ?>"
      enctype="multipart/form-data"
    >

      <!-- Método HTTP spoofing -->
      <input type="hidden" name="_method" value="PUT">


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
              <?= (($old['status'] ?? '') === 'ativo') ? 'selected' : '' ?>
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
          Trocar Logo

          <span class="label-helper">
            (opcional — manter vazio para não alterar)
          </span>
        </label>


        <!-- =========================================================
             PREVIEW DA LOGO ATUAL
        ========================================================= -->
        <?php if (!empty($viacao->logo) && file_exists($uploadBase . $viacao->logo)): ?>

          <div class="logo-preview">

            <img
              src="/uploads/logos/<?= htmlspecialchars($viacao->logo) ?>"
              alt="Logo atual"
            >

            <div>

              <strong>
                Logo atual
              </strong>

              <small>
                A imagem será substituída se um novo arquivo for enviado.
              </small>

            </div>

          </div>

        <?php endif; ?>


        <!-- =========================================================
             INPUT DE UPLOAD
        ========================================================= -->
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

        <!-- BOTÃO SALVAR -->
        <button type="submit" class="btn btn-primary">
          Salvar Alterações
        </button>

      </div>

    </form>

  </div>

</div>

<script src="/script.js"></script>
</body>
</html>
