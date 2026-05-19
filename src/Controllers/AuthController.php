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
      'erro' => null,
      'notice' => $notice,
    ]);
  }

  public function login(): void
  {
    $this->startSession();

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? ''); // Alterado de 'senha' para 'password'

    if ($email === '' || $password === '') { // Alterado de $senha para $password
      View::render('auth/login', [
        'erro' => 'Preencha e-mail e senha.',
      ]);
      return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      View::render('auth/login', [
        'erro' => 'Informe um e-mail válido.',
      ]);
      return;
    }

    $repo = new UserRepository();

    $user = $repo->findByEmail($email);

    // Alterado de $senha para $password
    if (!$user || !password_verify($password, $user['password'])) {
      View::render('auth/login', [
        'erro' => 'E-mail ou senha inválidos.',
      ]);
      return;
    }

    session_regenerate_id(true);

    $_SESSION['auth'] = true;

    $_SESSION['user'] = [
      'id' => $user['id'],
      'nome' => $user['nome'],
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
        session_name(),
        '',
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
