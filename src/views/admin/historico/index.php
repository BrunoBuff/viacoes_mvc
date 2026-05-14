<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Histórico de Alterações</title>
  <link rel="stylesheet" href="/styles.css">
</head>

<body class="admin-page">

<div class="page-container">

  <!-- =========================================================
       HEADER DA PÁGINA
  ========================================================= -->
  <header class="page-header">

    <div>
      <h1 class="page-title">
        Histórico de Alterações
      </h1>

      <p class="page-subtitle">
        Registro de todas as ações realizadas na plataforma
      </p>
    </div>

  </header>


  <!-- =========================================================
       ACTIONS
  ========================================================= -->
  <section class="header-actions">

    <div class="header-buttons">

      <a href="/admin/viacoes" class="btn btn-secondary">
        Voltar para a lista
      </a>

    </div>

  </section>


  <!-- =========================================================
       TABLE
  ========================================================= -->
  <div class="table-wrapper">

    <?php if (empty($historico)): ?>

      <table class="table">
        <tbody>
        <tr>
          <td class="empty-row">
            Nenhuma ação registrada ainda.
          </td>
        </tr>
        </tbody>
      </table>

    <?php else: ?>

      <table class="table">

        <thead>
        <tr>
          <th>Data/Hora</th>
          <th>Viação</th>
          <th>Ação</th>
          <th>Detalhes</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($historico as $log): ?>

          <tr>

            <!-- DATA/HORA -->
            <td>
              <?= date('d/m/Y H:i', strtotime($log->dataHora)) ?>
            </td>

            <!-- VIAÇÃO -->
            <td>
              <strong><?= htmlspecialchars($log->nomeViacao ?? '—') ?></strong>
            </td>

            <!-- AÇÃO -->
            <td>
              <?php
              $classe = match(strtolower($log->acao)) {
                'criado'   => 'criou',
                'editado'  => 'editou',
                'excluido' => 'excluiu',
                default    => '',
              };
              ?>
              <span class="<?= $classe ?>">
                  <?= htmlspecialchars($log->acao) ?>
                </span>
            </td>

            <!-- DETALHES -->
            <td class="detalhes-col">
              <?= nl2br(htmlspecialchars($log->detalhes)) ?>
            </td>

          </tr>

        <?php endforeach; ?>
        </tbody>

      </table>

    <?php endif; ?>

  </div>

</div>

<script src="/script.js"></script>
</body>
</html>
