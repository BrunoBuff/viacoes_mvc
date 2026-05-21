<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\UserRepository;

final class AuthController
{

//* ------------------- showLogin -------------------  *//
  public function showLogin(): void
  {
    $this->startSession();                                      // inicia sessão caso não exista

    $notice = $_SESSION['auth_notice'] ?? null;                 // recupera aviso da sessão
    unset($_SESSION['auth_notice']);                            // remove aviso após exibir

    View::render('auth/login', [                                // renderiza tela de login
      'erro'   => null,
      'notice' => $notice,
    ]);
  }

//* --------------------- Login ---------------------  *//
  public function login(): void
  {
    $this->startSession();                                      // inicia sessão caso não exista

    $email    = trim($_POST['email']    ?? '');                 // captura e limpa email
    $password = trim($_POST['password'] ?? '');                 // captura e limpa senha

    if ($email === '' || $password === '') {                    // se vazio valida campos vazios e retorna view
      View::render('auth/login', ['erro' => 'Preencha e-mail e senha.', 'notice' => null]);
      return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {           // valida email retorna view
      View::render('auth/login', ['erro' => 'Informe um e-mail válido.', 'notice' => null]);
      return;
    }

    $repo = new UserRepository();                               // busca usuário por no banco
    $user = $repo->findByEmail($email);                         // procura usuário pelo email

    if (!$user || !password_verify($password, $user['password'])) { // validação de usuário e senha utilizando passwor_verify
      View::render('auth/login', ['erro' => 'E-mail ou senha inválidos.', 'notice' => null]);
      return;
    }

    //* --- Segurança --- *//
    session_regenerate_id(true);                                // troca o id da sessão

    $_SESSION['auth'] = true;                                   // flag de login

    $_SESSION['user_id'] = (int) $user['id'];                   // guarda somente o id

    $_SESSION['user'] = [                                       // guarda os outros dados
      'id'    => (int) $user['id'],
      'nome'  => $user['nome'],
      'email' => $user['email'],
    ];

    $intended = $_SESSION['intended_url'] ?? '/admin/viacoes';  // Verifica se havia página protegida pendente
    unset($_SESSION['intended_url']);                           // Remove intended_url

    $this->redirect($intended);                                 // redireciona usuário
  }

//* --------------------- Logout ---------------------  *//
  public function logout(): void
  {
    $this->startSession();                                      // inicia sessão caso não exista

    $_SESSION = [];                                             // esvazia conteudo

    if (ini_get('session.use_cookies')) {                       // verifica se sessão usa cookies
      $params = session_get_cookie_params();                    // recupera parametros do cookie

      setcookie(                                                // apaga cookie no navegador
        session_name(), '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
      );
    }

    session_destroy();                                          // destroi sessão no servidor
    $this->redirect('/');                                       // redireciona para login
  }

//* --------------------- Check ---------------------  *//
  public static function check(): bool
  {
    if (session_status() === PHP_SESSION_NONE) {                // verifica se sessão não iniciou
      session_start();                                          // inicia sessão
    }

    return isset($_SESSION['auth']) && $_SESSION['auth'] === true; // verifica se usuário está autenticado
  }

//* ---------------------- User ----------------------  *//
  public static function user(): ?array
  {
    if (!self::check()) {                                       // verifica se usuário está logado
      return null;
    }

    return $_SESSION['user'] ?? null;                           // retorna dados do usuário
  }

//* ------------------ startSession ------------------  *//
  private function startSession(): void
  {
    if (session_status() === PHP_SESSION_NONE) {                // verifica se sessão não iniciou
      session_start();                                          // inicia sessão
    }
  }

//* -------------------- Redirect --------------------  *//
  private function redirect(string $url): void
  {
    header("Location: {$url}");                                 // envia redirecionamento
    exit;                                                       // encerra execução
  }
}
