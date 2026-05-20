<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administração de Usuários</title>
  <link rel="stylesheet" href="/styles.css">
</head>

<body class="admin-page">
<div class="page-container">

  <!-- HEADER -->
  <header class="page-header">
    <div>
      <h1 class="page-title">Gerenciamento de Usuários</h1>
      <p class="page-subtitle">Cadastre, edite e gerencie os usuários do sistema</p>
    </div>
  </header>

  <!-- FILTROS / AÇÕES -->
  <section class="header-actions">

    <div class="header-buttons">
      <a href="/admin/viacoes" class="btn btn-secondary">Voltar para a lista</a>
      <a href="/admin/usuarios/create" class="btn">Novo Usuário</a>
    </div>

    <form method="GET" action="/admin/usuarios" class="form-busca">
      <input
        type="text"
        name="busca"
        value="<?= htmlspecialchars($filtros['busca'] ?? '') ?>"
        placeholder="Buscar por nome ou e-mail..."
      >

      <button type="submit" class="btn btn-search">Filtrar</button>

      <?php if (!empty($filtros['busca'])): ?>
        <a href="/admin/usuarios" class="btn-limpar">Limpar</a>
      <?php endif; ?>
    </form>

  </section>

  <!-- TABELA -->
  <div class="table-wrapper">

    <?php if (empty($usuarios)): ?>

      <table class="table">
        <tbody>
        <tr>
          <td colspan="4" class="empty-row">Nenhum usuário encontrado.</td>
        </tr>
        </tbody>
      </table>

    <?php else: ?>

      <table class="table">
        <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>E-mail</th>
          <th>Ações</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($usuarios as $usuario): ?>
          <tr>
            <td><?= (int) $usuario->id ?></td>
            <td><strong><?= htmlspecialchars($usuario->nome) ?></strong></td>
            <td><?= htmlspecialchars($usuario->email) ?></td>

            <td class="actions-cell">
              <a href="/admin/usuarios/<?= $usuario->id ?>/edit" class="btn btn-small">
                Editar
              </a>

              <form
                method="POST"
                action="/admin/usuarios/<?= $usuario->id ?>"
                style="display:inline;"
                onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');"
              >
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-danger btn-small">
                  Excluir
                </button>
              </form>
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
