<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\UserRepository;

final class AuthController
{
  public function showLogin(): void
  {
    $this->startSession();

    $notice = $_SESSION['auth_notice'] ?? null;
    unset($_SESSION['auth_notice']);

    View::render('auth/login', [
      'erro'   => null,
      'notice' => $notice,
    ]);
  }

  public function login(): void
  {
    $this->startSession();

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
      View::render('auth/login', ['erro' => 'Preencha e-mail e senha.', 'notice' => null]);
      return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      View::render('auth/login', ['erro' => 'Informe um e-mail válido.', 'notice' => null]);
      return;
    }

    $repo = new UserRepository();
    $user = $repo->findByEmail($email);

    if (!$user || !password_verify($password, $user['password'])) {
      View::render('auth/login', ['erro' => 'E-mail ou senha inválidos.', 'notice' => null]);
      return;
    }

    session_regenerate_id(true);

    $_SESSION['auth'] = true;

    // CORREÇÃO: gravamos user_id como chave de primeiro nível para que
    // ViacaoService possa recuperá-lo com $_SESSION['user_id'].
    // A versão anterior só gravava $_SESSION['user']['id'], mas o Service
    // lia $_SESSION['user_id'] — causando fallback silencioso para userId=1.
    $_SESSION['user_id'] = (int) $user['id'];

    $_SESSION['user'] = [
      'id'    => (int) $user['id'],
      'nome'  => $user['nome'],
      'email' => $user['email'],
    ];

    $intended = $_SESSION['intended_url'] ?? '/admin/viacoes';
    unset($_SESSION['intended_url']);

    $this->redirect($intended);
  }

  public function logout(): void
  {
    $this->startSession();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(), '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
      );
    }

    session_destroy();
    $this->redirect('/');
  }

  public static function check(): bool
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
  }

  public static function user(): ?array
  {
    if (!self::check()) {
      return null;
    }

    return $_SESSION['user'] ?? null;
  }

  private function startSession(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  private function redirect(string $url): void
  {
    header("Location: {$url}");
    exit;
  }
}
