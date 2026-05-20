// ================================================================
// AUTOCOMPLETE — Cidades
// ================================================================

const cidades = [
  "Cabo Frio, RJ", "Rio de Janeiro, RJ", "São Paulo, SP",
  "Belo Horizonte, MG", "Curitiba, PR", "Arraial do Cabo, RJ", "Búzios, RJ"
];

function configurarAutocomplete(idInput, idLista) {
  const input = document.getElementById(idInput);
  const lista = document.getElementById(idLista);

  if (!input || !lista) return;

  input.addEventListener('input', function () {
    const valorDigitado = this.value.toLowerCase();
    lista.innerHTML = '';

    if (valorDigitado.length === 0) { lista.style.display = 'none'; return; }

    const sugestoes = cidades.filter(c => c.toLowerCase().includes(valorDigitado));
    if (sugestoes.length === 0) { lista.style.display = 'none'; return; }

    lista.style.display = 'block';
    sugestoes.forEach(cidade => {
      const li = document.createElement('li');
      li.textContent = cidade;
      li.addEventListener('click', () => { input.value = cidade; lista.style.display = 'none'; });
      lista.appendChild(li);
    });
  });

  document.addEventListener('click', function (e) {
    if (e.target !== input) lista.style.display = 'none';
  });
}

// CORREÇÃO: IDs dos elementos de autocomplete (lista-origem e lista-destino)
// estavam referenciados no JS mas as <ul> não existiam no HTML original da home.
// As <ul> foram adicionadas na view home.php corrigida.
configurarAutocomplete('origem', 'lista-origem');
configurarAutocomplete('destino', 'lista-destino');


// ================================================================
// BOTÃO INVERTER (origem <-> destino)
// ================================================================

const botaoInverter = document.querySelector('.botao-inverter');
const inputOrigem   = document.getElementById('origem');
const inputDestino  = document.getElementById('destino');

if (botaoInverter && inputOrigem && inputDestino) {
  botaoInverter.addEventListener('click', () => {
    const tmp          = inputOrigem.value;
    inputOrigem.value  = inputDestino.value;
    inputDestino.value = tmp;
    botaoInverter.style.transform =
      botaoInverter.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
  });
}


// ================================================================
// VALIDAÇÃO DE DATAS
// ================================================================

const campoSaida   = document.getElementById('data-saida');
const campoRetorno = document.getElementById('data-retorno');

if (campoSaida && campoRetorno) {
  const hoje = new Date().toISOString().split('T')[0];
  campoSaida.setAttribute('min', hoje);
  campoRetorno.setAttribute('min', hoje);

  campoSaida.addEventListener('change', function () {
    campoRetorno.setAttribute('min', campoSaida.value);
    if (campoRetorno.value && campoRetorno.value < campoSaida.value) {
      campoRetorno.value = '';
      alert('Atenção: A data de retorno não pode ser anterior à data de saída.');
    }
  });
}


// ================================================================
// VALIDAÇÃO EM TEMPO REAL DO CAMPO URL (admin)
// ================================================================

const urlInput = document.getElementById('url');
const urlError = document.getElementById('url-error');

if (urlInput && urlError) {
  urlInput.addEventListener('input', function () {
    const valor = urlInput.value.trim();
    if (valor === '') { urlError.textContent = ''; return; }
    try {
      const parsed = new URL(valor);
      if (!['http:', 'https:'].includes(parsed.protocol)) throw new Error();
      urlError.textContent = '';
    } catch {
      urlError.textContent = 'URL inválida — ex: https://minha-viacao.com.br';
    }
  });
}


// ================================================================
// FILTRO LIVE DE VIAÇÕES (home)
// Filtra os cards client-side sem recarregar a página.
// ================================================================

(function () {
  const input    = document.getElementById('filtro-viacao');
  const grid     = document.getElementById('viacoes-grid');
  const vazio    = document.getElementById('viacoes-vazia');
  const badge    = document.getElementById('badge-ativas');
  const termoEl  = document.getElementById('filtro-termo');
  const btnLimpar = document.getElementById('btn-limpar-filtro');

  if (!input || !grid) return;

  const cards = Array.from(grid.querySelectorAll('.viacao-card'));
  const totalOriginal = cards.length;

  function atualizar(termo) {
    const q = termo.trim().toLowerCase();

    let visiveis = 0;

    cards.forEach(card => {
      const nome   = card.dataset.nome   || '';
      const cidade = card.dataset.cidade || '';
      const bate   = q === '' || nome.includes(q) || cidade.includes(q);

      card.style.display = bate ? '' : 'none';
      if (bate) visiveis++;
    });

    // Mostra/oculta mensagem de vazio
    if (vazio) {
      if (visiveis === 0) {
        if (termoEl) termoEl.textContent = termo.trim();
        vazio.style.display = '';
      } else {
        vazio.style.display = 'none';
      }
    }

    // Atualiza badge de contagem
    if (badge) {
      const n   = visiveis;
      badge.textContent = `${n} viação${n !== 1 ? 'ões' : ''} ativa${n !== 1 ? 's' : ''}`;
    }

    // Mostra/oculta botão limpar
    if (btnLimpar) {
      btnLimpar.style.display = q !== '' ? '' : 'none';
    }
  }

  // Inicializa com o valor já preenchido (vindo do PHP, ex: após reload)
  atualizar(input.value);

  input.addEventListener('input', () => atualizar(input.value));

  if (btnLimpar) {
    btnLimpar.addEventListener('click', (e) => {
      e.preventDefault();
      input.value = '';
      atualizar('');
    });
  }
})();


// ================================================================
// TEMA DARK / LIGHT
// ================================================================

// Aplica tema salvo ANTES do DOMContentLoaded para evitar flash
if (localStorage.getItem('theme') === 'dark') {
  document.body.classList.add('dark-mode');
}

document.addEventListener('DOMContentLoaded', () => {
  const themeToggle = document.getElementById('theme-toggle');

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      const isDark = document.body.classList.contains('dark-mode');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
  }
});
