<?php
// A lógica do banco deve preferencialmente estar no Controller,
// mas se estiver seguindo um modelo simples, fica no topo do arquivo:
$stmtViacoes = $pdo->query("SELECT * FROM viacoes WHERE status = 'ativo' ORDER BY nome ASC");
$viacoesAtivas = $stmtViacoes->fetchAll();

$stmtTotal = $pdo->query("SELECT COUNT(*) FROM viacoes WHERE status = 'ativo'");
$totalViacoes = (int) $stmtTotal->fetchColumn();
?>

<section class="banner">
  <div class="container banner-content">
    <div class="search-box">
      <h2>Comprar Passagens de Ônibus</h2>
      <form>
      </form>
    </div>
  </div>
</section>

<section class="beneficios">
</section>

<section class="secao-viacoes">
  <div class="container">
    <div class="secao-header">
      <h2>Nossas Viações Parceiras</h2>
      <?php if ($totalViacoes > 0): ?>
        <span class="badge-ativas"><?= $totalViacoes ?> viações ativas</span>
      <?php endif; ?>
    </div>

    <div class="viacoes-grid">
      <?php foreach ($viacoesAtivas as $v): ?>
        <a href="<?= $v['url'] ?>" class="viacao-card">
          <span class="nome-viacao"><?= $v['nome'] ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
