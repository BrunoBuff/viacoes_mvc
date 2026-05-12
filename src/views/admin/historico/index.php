<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Histórico de Alterações</title>
  <link rel="stylesheet" href="/styles.css">
</head>
<body>

<h1>Histórico de Alterações</h1>

<a href="/admin/viacoes">← Voltar para a lista</a>

<br><br>

<?php if (empty($historico)): ?>
  <p style="color:#666">Nenhuma ação registrada ainda.</p>
<?php else: ?>
  <table>
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
        <td><?= date('d/m/Y H:i', strtotime($log->dataHora)) ?></td>
        <td><strong><?= htmlspecialchars($log->nomeViacao ?? '—') ?></strong></td>
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
        <td class="detalhes-col">
          <?= nl2br(htmlspecialchars($log->detalhes)) ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

</body>
</html>
