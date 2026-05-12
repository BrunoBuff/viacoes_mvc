// Array para AutoComplete
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

configurarAutocomplete('origem', 'lista-origem');
configurarAutocomplete('destino', 'lista-destino');

// BOTÃO INVERTER
const botaoInverter = document.querySelector('.botao-inverter');
const inputOrigem   = document.getElementById('origem');
const inputDestino  = document.getElementById('destino');

if (botaoInverter && inputOrigem && inputDestino) {
  botaoInverter.addEventListener('click', () => {
    const tmp = inputOrigem.value;
    inputOrigem.value  = inputDestino.value;
    inputDestino.value = tmp;
    botaoInverter.style.transform =
      botaoInverter.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
  });
}

// Validação de datas
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

// Validação em tempo real do campo URL (usado nos forms do admin)
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

// Tema Dark

const themeToggle = document.getElementById('theme-toggle');
const themeText = document.getElementById('theme-text');

function enableDarkMode() {
  document.body.classList.add('dark-mode');
  localStorage.setItem('theme', 'dark');
  if (themeText) themeText.textContent = 'Modo Claro';
}

function disableDarkMode() {
  document.body.classList.remove('dark-mode');
  localStorage.setItem('theme', 'light');
  if (themeText) themeText.textContent = 'Modo Escuro';
}

// Verifica a preferência ao carregar a página
if (localStorage.getItem('theme') === 'dark') {
  enableDarkMode();
}

if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    if (document.body.classList.contains('dark-mode')) {
      disableDarkMode();
    } else {
      enableDarkMode();
    }
  });
}
