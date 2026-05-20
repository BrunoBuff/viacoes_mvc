<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quero Passagem — Passagens de Ônibus Online</title>
  <meta name="description" content="Compre passagens de ônibus online com segurança. Mais de 5 mil destinos em todo o Brasil.">
  <link rel="stylesheet" href="/style.css">
</head>
<body>

<?php
$isLoggedIn    = \App\Controllers\AuthController::check();
$usuarioLogado = \App\Controllers\AuthController::user();
?>

<!-- HEADER -->
<header class="header">
  <div class="container header-content">
    <div class="logo">
      <img src="https://assets.queropassagem.com.br/static/Images/Logos/logo_nova_grande.png" alt="Quero Passagem">
    </div>
    <nav class="menu">
      <a href="#" class="menu-item active">
        <img src="https://queropassagem.com.br/2020/images/icones/rodoviario.svg?1709231149" alt=""> Passagens
      </a>
      <a href="#" class="menu-item">
        <img src="https://queropassagem.com.br/2020/images/icones/hotel.svg?1709231149" alt=""> Hotéis
        <span class="novo">Novo</span>
      </a>
    </nav>
    <div class="actions">
      <a href="#" class="help">Central de Ajuda</a>

      <?php if ($isLoggedIn): ?>
        <span class="usuario-nome">Olá, <?= htmlspecialchars($usuarioLogado['nome'] ?? 'Admin') ?></span>
        <a href="/admin/viacoes" class="btn-login">Painel Admin</a>
        <a href="/logout" class="btn-logout">Sair</a>
      <?php else: ?>
        <a href="/login" class="btn-login">Entrar</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- BANNER / HERO -->
<section class="banner">
  <div class="container banner-content">
    <div class="search-box">
      <h2>Comprar Passagens de Ônibus</h2>
      <form>
        <div class="input-container">
          <label for="origem">Partindo de</label>
          <input type="text" id="origem" name="origem" placeholder="Cidade de origem">
          <ul id="lista-origem" class="autocomplete-lista"></ul>
        </div>
        <div class="input-container">
          <label for="destino">Indo para</label>
          <input type="text" id="destino" name="destino" placeholder="Cidade de destino">
          <ul id="lista-destino" class="autocomplete-lista"></ul>
        </div>
        <div class="dates-row">
          <div class="input-container">
            <label for="data-saida">Data Saída</label>
            <input type="date" id="data-saida" name="data_saida">
          </div>
          <div class="input-container">
            <label for="data-retorno">Data Retorno</label>
            <input type="date" id="data-retorno" name="data_retorno">
          </div>
        </div>
        <button type="submit" class="btn-search">Buscar Passagem</button>
      </form>
    </div>
  </div>
</section>

<!-- BENEFÍCIOS -->
<section class="beneficios">
  <div class="container-beneficios">
    <div class="beneficio">
      <div class="beneficio-texto">
        <h3>✅ Viagens Seguras</h3>
        <p>Mais de 30 milhões de compras realizadas</p>
      </div>
    </div>
    <div class="beneficio">
      <div class="beneficio-texto">
        <h3>💳 Pagamento Flexível</h3>
        <p>Pague com Pix, Nupay ou em até 12x</p>
      </div>
    </div>
    <div class="beneficio">
      <div class="beneficio-texto">
        <h3>↩️ Cancelamento Fácil</h3>
        <p>Passagens flexíveis e atendimento personalizado</p>
      </div>
    </div>
  </div>
</section>

<!-- =========================================================
     VIAÇÕES PARCEIRAS — com filtro live
========================================================= -->
<section class="secao-viacoes" id="secao-viacoes">
  <div class="container">

    <div class="secao-header">
      <h2>Nossas Viações Parceiras</h2>
      <p>Empresas de ônibus cadastradas e verificadas pela nossa equipe</p>

      <?php if (!empty($viacoesAtivas)): ?>
        <span class="badge-ativas" id="badge-ativas">
          <?= count($viacoesAtivas) ?> viação<?= count($viacoesAtivas) > 1 ? 'ões' : '' ?> ativa<?= count($viacoesAtivas) > 1 ? 's' : '' ?>
        </span>
      <?php endif; ?>
    </div>

    <!-- Campo de filtro -->
    <div class="filtro-viacoes">
      <input
        type="text"
        id="filtro-viacao"
        placeholder="Filtrar por nome ou cidade..."
        value="<?= htmlspecialchars($filtro ?? '') ?>"
        autocomplete="off"
        aria-label="Filtrar viações"
      >
      <?php if (!empty($filtro)): ?>
        <a href="/" class="btn-limpar-filtro" id="btn-limpar-filtro">✕ Limpar</a>
      <?php endif; ?>
    </div>

    <?php if ($erroConexao): ?>
      <div class="viacoes-vazia">
        <p>Não foi possível carregar as viações no momento.</p>
      </div>

    <?php elseif (empty($viacoesAtivas)): ?>
      <div class="viacoes-vazia" id="viacoes-vazia">
        <p>Nenhuma viação encontrada<?= !empty($filtro) ? ' para "' . htmlspecialchars($filtro) . '"' : '' ?>.</p>
        <?php if (empty($filtro)): ?>
          <a href="/admin/viacoes">Cadastrar primeira viação →</a>
        <?php endif; ?>
      </div>

    <?php else: ?>
      <?php
      $uploadBase = dirname(__DIR__, 2) . '/src/public/uploads/logos/';
      ?>
      <div class="viacoes-grid" id="viacoes-grid">
        <?php foreach ($viacoesAtivas as $v): ?>
          <a href="<?= htmlspecialchars($v->url) ?>"
             target="_blank" rel="noopener noreferrer"
             class="viacao-card"
             data-nome="<?= htmlspecialchars(mb_strtolower($v->nome, 'UTF-8')) ?>"
             data-cidade="<?= htmlspecialchars(mb_strtolower($v->cidade, 'UTF-8')) ?>"
             title="Visitar site da <?= htmlspecialchars($v->nome) ?>">

            <?php if (!empty($v->logo) && file_exists($uploadBase . $v->logo)): ?>
              <img src="/uploads/logos/<?= htmlspecialchars($v->logo) ?>"
                   alt="Logo <?= htmlspecialchars($v->nome) ?>">
            <?php else: ?>
              <div class="card-avatar">
                <?= strtoupper(mb_substr($v->nome, 0, 1, 'UTF-8')) ?>
              </div>
            <?php endif; ?>

            <span class="nome-viacao"><?= htmlspecialchars($v->nome) ?></span>
            <?php if (!empty($v->cidade)): ?>
              <span class="cidade-viacao"><?= htmlspecialchars($v->cidade) ?></span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Mensagem exibida pelo JS quando o filtro não retorna resultados -->
      <div class="viacoes-vazia" id="viacoes-vazia" style="display:none;">
        <p>Nenhuma viação encontrada para "<span id="filtro-termo"></span>".</p>
      </div>

    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
      <div style="margin-top: 36px; text-align: center;">
        <a href="/admin/viacoes" class="btn-outline">Gerenciar Viações</a>
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- DESTINOS -->
<section class="destinos">
  <div class="container">
    <div class="text">
      <div class="selection-title">Escolha seu Destino</div>
      <div class="selection-info">São mais de 5 mil destinos em todo o país para escolher sem sair de casa.</div>
    </div>
    <div class="cards-grid">
      <div class="card">
        <img src="https://assets.queropassagem.com.br/public/Upload/cidades/1a.jpg" alt="São Paulo">
        <div class="card-info">
          <h3>São Paulo</h3>
          <div class="rotas-titulo"><span>Partindo de</span><span>A partir de</span></div>
          <div class="rota"><p>Rio de Janeiro, RJ</p><p>R$ 104</p></div>
          <div class="rota"><p>Belo Horizonte, MG</p><p>R$ 139</p></div>
          <div class="rota"><p>Ribeirão Preto, SP</p><p>R$ 144</p></div>
          <div class="rota"><p>Sorocaba, SP</p><p>R$ 44</p></div>
        </div>
      </div>
      <div class="card">
        <img src="https://assets.queropassagem.com.br/public/Upload/cidades/57a.jpg" alt="Rio de Janeiro">
        <div class="card-info">
          <h3>Rio de Janeiro</h3>
          <div class="rotas-titulo"><span>Partindo de</span><span>A partir de</span></div>
          <div class="rota"><p>São Paulo, SP</p><p>R$ 105</p></div>
          <div class="rota"><p>Belo Horizonte, MG</p><p>R$ 109</p></div>
          <div class="rota"><p>Cabo Frio, RJ</p><p>R$ 75</p></div>
          <div class="rota"><p>Macaé, RJ</p><p>R$ 89</p></div>
        </div>
      </div>
      <div class="card">
        <img src="https://assets.queropassagem.com.br/public/Upload/cidades/55a.jpg" alt="Curitiba">
        <div class="card-info">
          <h3>Curitiba</h3>
          <div class="rotas-titulo"><span>Partindo de</span><span>A partir de</span></div>
          <div class="rota"><p>Florianópolis, SC</p><p>R$ 68</p></div>
          <div class="rota"><p>Porto Alegre, RS</p><p>R$ 27</p></div>
          <div class="rota"><p>Joinville, SC</p><p>R$ 38</p></div>
          <div class="rota"><p>Almirante Tamandaré</p><p>R$ 50</p></div>
        </div>
      </div>
    </div>
  </div>
  <a class="opcoes1">Mostrar Mais Destinos</a>
</section>

<!-- RAPAZ -->
<div class="rapaz">
  <img src="https://assets.queropassagem.com.br/static/Images/banner_download_app_2.png" class="propaganda" alt="Viaje com a Quero Passagem">
</div>

<!-- TOP 15 TRECHOS -->
<section class="trechos">
  <div class="container">
    <div class="text">
      <div class="trechos">Top 15 Trechos de Ônibus</div>
      <div class="procurados">Os trechos mais procurados em nossa Central de Passagens.</div>
    </div>
    <div class="cards-grid">
      <div class="card">
        <div class="card-info">
          <div class="rotas-titulo"><span>Partindo de</span><span>Indo para</span></div>
          <div class="rota"><p>Rio de Janeiro</p><p>São Paulo</p></div>
          <div class="rota"><p>São Paulo</p><p>Rio de Janeiro</p></div>
          <div class="rota"><p>São Paulo</p><p>Curitiba</p></div>
          <div class="rota"><p>Curitiba</p><p>São Paulo</p></div>
          <div class="rota"><p>Brasília</p><p>Goiânia</p></div>
        </div>
      </div>
      <div class="card">
        <div class="card-info">
          <div class="rotas-titulo"><span>Partindo de</span><span>Indo para</span></div>
          <div class="rota"><p>Goiânia</p><p>Brasília</p></div>
          <div class="rota"><p>São Paulo</p><p>Goiânia</p></div>
          <div class="rota"><p>Belo Horizonte</p><p>São Paulo</p></div>
          <div class="rota"><p>Goiânia</p><p>São Paulo</p></div>
          <div class="rota"><p>São Paulo</p><p>Belo Horizonte</p></div>
        </div>
      </div>
      <div class="card">
        <div class="card-info">
          <div class="rotas-titulo"><span>Partindo de</span><span>Indo para</span></div>
          <div class="rota"><p>Florianópolis</p><p>Curitiba</p></div>
          <div class="rota"><p>São Paulo</p><p>Londrina</p></div>
          <div class="rota"><p>Porto Alegre</p><p>Curitiba</p></div>
          <div class="rota"><p>Curitiba</p><p>Florianópolis</p></div>
          <div class="rota"><p>São Paulo</p><p>Bauru</p></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PARCEIRO -->
<div class="parceiro">
  <div class="left">
    <img src="https://assets.queropassagem.com.br/static/Images/parceiro.png" alt="Parceiros Quero Passagem">
  </div>
  <div class="right">
    <div class="vantagens">
      <img src="https://queropassagem.com.br/images/agencia.png" alt="Agência de Viagem">
      <div class="info">
        <span class="titulo">Agência de Viagem</span>
        <span class="descricao">Sistema completo de emissão e venda de passagens rodoviárias para agência de viagens.</span>
      </div>
    </div>
    <div class="vantagens">
      <img src="https://queropassagem.com.br/images/otas.png" alt="OTAs">
      <div class="info">
        <span class="titulo">OTA's</span>
        <span class="descricao">Insira nosso banner (buscador de passagens) em seu site e ganhe comissões por cada venda.</span>
      </div>
    </div>
    <button class="saiba-mais">Saiba mais</button>
  </div>
</div>

<!-- NEWSLETTER -->
<div class="email">
  <h2>Deseja receber e-mails com novidades e descontos exclusivos?</h2>
  <div class="nome">
    <div class="pessoa">
      <span class="icon">👤</span>
      <input type="text" placeholder="Seu nome">
    </div>
    <div class="email-input">
      <span class="icon">✉️</span>
      <input type="email" placeholder="Seu e-mail">
    </div>
    <button class="inscreva">Inscreva-se</button>
  </div>
</div>

<!-- POR QUE VIAJAR -->
<section class="mulheres">
  <div class="container-viagem">
    <div class="viagem-titulo">
      <h2 class="viajar">Por que viajar com a Quero Passagem?</h2>
      <p class="portal">
        A Quero Passagem é o maior Portal de Passagens de Ônibus do Brasil. Pesquise viações, compare horários,
        preços e compre passagens rodoviárias sem sair de casa. São mais de 5 mil destinos em todo o país,
        conectando cidades como Belo Horizonte, Curitiba, Brasília, São Paulo, Rio de Janeiro, Salvador, Goiânia e muito mais.
      </p>
    </div>
    <div class="cards-grid-pagamento">
      <div class="card">
        <img src="https://assets.queropassagem.com.br/static/Images/card_pagamento.png" alt="Formas de Pagamento">
        <div class="card-info">
          <div class="card-titulo"><span>Pagamento</span></div>
          <div class="descricao-info"><p>Escolha a melhor forma de pagamento: em até 12x no cartão de crédito, débito, boleto ou via Pix.</p></div>
        </div>
      </div>
      <div class="card">
        <img src="https://samaria.com.br/wp-content/uploads/2025/05/uma-mulher-linda-dormir-no-autocarro-scaled.jpg" alt="Conforto">
        <div class="card-info">
          <div class="card-titulo"><span>Conforto</span></div>
          <div class="descricao-info"><p>Viaje com conforto e segurança nas melhores companhias do Brasil, com mais de 350 viações parceiras.</p></div>
        </div>
      </div>
      <div class="card">
        <img src="https://img.nsctotal.com.br/wp-content/uploads/2022/10/BuscaOnibus-1.jpg" alt="Passagem">
        <div class="card-info">
          <div class="card-titulo"><span>Passagem</span></div>
          <div class="descricao-info"><p>Escolha horário, assento e empresa favorita. Finalize sua compra de forma rápida, segura e sem complicação.</p></div>
        </div>
      </div>
      <div class="card">
        <img src="https://catracalivre.com.br/wp-content/uploads/2022/10/istock-1065524348.jpg" alt="Confiança">
        <div class="card-info">
          <div class="card-titulo"><span>Confiança</span></div>
          <div class="descricao-info"><p>Mais de 15 milhões de passageiros na estrada. Compre sua passagem em menos de 5 minutos.</p></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq">
  <div class="faq-container">
    <h2>Perguntas Frequentes</h2>
    <details class="faq-item">
      <summary>Quero Passagem é seguro para comprar passagens de ônibus online?</summary>
      <p>Sim! A plataforma utiliza tecnologia de proteção de dados e pagamentos confiáveis para garantir sua segurança em cada compra.</p>
    </details>
    <details class="faq-item">
      <summary>Quero Passagem é confiável?</summary>
      <p>Sim! A Quero Passagem conecta você a diversas empresas de ônibus em todo o Brasil, permitindo comparar preços, horários e rotas para escolher a melhor opção.</p>
    </details>
    <details class="faq-item">
      <summary>Como cancelar minha passagem?</summary>
      <p>Acesse Minha Conta, localize sua passagem e siga as orientações. O pedido deve ser feito antes do horário da viagem e segue as regras da empresa de ônibus.</p>
    </details>
    <details class="faq-item">
      <summary>Como e onde vou receber a confirmação de compra da minha passagem?</summary>
      <p>Assim que o pagamento for aprovado, você recebe um e-mail com todos os detalhes da viagem.</p>
    </details>
    <details class="faq-item">
      <summary>Como alterar a data ou o horário da minha viagem?</summary>
      <p>Acesse Minha Conta, encontre sua passagem e solicite a mudança conforme as regras da empresa de ônibus.</p>
    </details>
    <details class="faq-item">
      <summary>Como usar o ID Jovem na reserva da passagem de ônibus?</summary>
      <p>Se você possui o ID Jovem, pode utilizar o benefício em viagens interestaduais pelo link: <a href="https://queropassagem.com.br/gratuidade">queropassagem.com.br/gratuidade</a></p>
    </details>
    <details class="faq-item">
      <summary>Quais são os meios de pagamento aceitos?</summary>
      <p>São aceitos cartão de crédito, Pix, Boleto, Transferência Bancária, Carteira Digital e outras opções disponíveis no momento da compra.</p>
    </details>
    <details class="faq-item">
      <summary>Quais são os documentos necessários para embarcar no ônibus?</summary>
      <p>Basta apresentar um documento oficial e físico com foto, como RG, CNH ou passaporte.</p>
    </details>
    <details class="faq-item">
      <summary>Qual limite de peso e com quantas bagagens posso embarcar?</summary>
      <p>Normalmente é permitido levar até 30 kg no bagageiro e até 5 kg de bagagem de mão, mas as regras podem variar por empresa.</p>
    </details>
  </div>
</section>

<!-- INFORMAÇÕES -->
<section class="informacoes">
  <div class="informacoes-container">
    <div class="coluna">
      <h3>Top Destinos</h3>
      <p>Ônibus Rio de Janeiro</p>
      <p>Ônibus São Paulo</p>
      <p>Ônibus Brasília</p>
      <p>Ônibus Campinas</p>
      <p>Ônibus Londrina</p>
      <span>+ Destinos</span>
    </div>
    <div class="coluna">
      <h3>Top Viações</h3>
      <p>Passagens Cometa</p>
      <p>Passagens Gontijo</p>
      <p>Passagens 1001</p>
      <p>Passagens Águia Branca</p>
      <p>Passagens Pássaro Marron</p>
      <span>+ Viações</span>
    </div>
    <div class="coluna">
      <h3>Top Rodoviárias</h3>
      <p>Rodoviária São Paulo - Tietê</p>
      <p>Rodoviária Rio de Janeiro - Novo Rio</p>
      <p>Rodoviária Belo Horizonte</p>
      <p>Rodoviária Curitiba</p>
      <p>Rodoviária São Paulo - Barra Funda</p>
      <span>+ Rodoviárias</span>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-top container">
    <div class="footer-about">
      <img src="https://assets.queropassagem.com.br/static/Images/Logos/logo_nova_grande.png"
           alt="Logo Quero Passagem" class="footer-logo">
      <p class="seguro">Na Quero Passagem sua compra é totalmente segura!</p>
      <p class="descricao">
        Para garantirmos que seus dados estejam sempre protegidos, não armazenamos nenhuma informação
        do cartão de crédito utilizado, seguindo os protocolos de criptografia e segurança das
        principais instituições bancárias do Brasil.
      </p>
      <p class="titulo-redes">Siga Nossas Redes Sociais:</p>
      <div class="redes">
        <img src="/assets/img/redes.png" alt="Redes sociais Quero Passagem">
      </div>
    </div>
    <div class="footer-links">
      <div class="col-links">
        <a>Sobre nós</a><a>Termos de uso</a><a>Política de privacidade</a>
        <a>Termos de Uso Lounge Vip</a><a>Imprensa</a><a>Minha Conta</a>
      </div>
      <div class="col-links">
        <a>Atendimento Online</a><a>Trabalhe Conosco</a><a>Gratuidade</a>
        <a>Auto Viações</a><a>Rodoviárias</a><a>Destinos</a>
      </div>
      <div class="col-links">
        <a>Afiliados</a><a>Versão Mobile</a><a>Rodomilhas</a>
        <a>Viajo Mucho</a><a>La Terminal Costa Rica</a>
      </div>
    </div>
    <div class="grupo">
      <p class="titulo-grupo">Conheça o Grupo QP:</p>
      <div class="logos-grupo">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/rodoviaria-online.svg" alt="Rodoviária Online">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/viajo-mucho.svg" alt="Viajo Mucho">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/la-terminal.svg" alt="La Terminal">
      </div>
    </div>
  </div>

  <div class="linha"></div>

  <div class="pagamentos container">
    <div class="formas">
      <p class="titulo">Formas de Pagamento</p>
      <div class="icones">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/Pagamento/mastercard.svg" alt="Mastercard">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/Pagamento/visa.svg" alt="Visa">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/Pagamento/hipercard.svg" alt="Hipercard">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/Pagamento/american.svg" alt="American Express">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/Pagamento/elo.svg" alt="Elo">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/Pagamento/pix.svg" alt="Pix">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/Pagamento/mercado-pago.svg" alt="Mercado Pago">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/Pagamento/boleto.png" alt="Boleto">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/Pagamento/nupay.svg" alt="Nupay">
      </div>
    </div>
    <div class="seguranca">
      <p class="titulo">Segurança</p>
      <div class="icones">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/cadastur.svg" alt="Cadastur">
        <img src="https://assets.queropassagem.com.br/static/Images/Icones/compra-segura.png" alt="Compra Segura">
      </div>
    </div>
  </div>

  <div class="footer-bottom container">
    <p>
      Calçada das Margaridas, 163 — Sala 02 — Condomínio Centro Comercial Alphaville,
      Barueri - SP | CEP: 06453-038 | CNPJ: 18.087.991/0001-57 |
      saconibus@queropassagem.com.br
    </p>
    <p class="copyright">Copyright 2026 © QueroPassagem.com.br</p>
  </div>
</footer>

<script src="/script.js"></script>
</body>
</html>
