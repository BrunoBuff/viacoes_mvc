<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class AuthController
{
  /**
   * =========================================================
   * Exibe tela de login
   * =========================================================
   */
  public function showLogin(): void
  {
    $this->startSession();

    // Se já estiver autenticado
    if ($this->isAuthenticated()) {
      $this->redirect('/admin/viacoes');
    }

    View::render('auth/login', [
      'erro' => null
    ]);
  }

  /**
   * =========================================================
   * Realiza login
   * =========================================================
   */
  public function login(): void
  {
    $this->startSession();

    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    // =====================================================
    // Validação básica
    // =====================================================

    if ($email === '' || $senha === '') {

      View::render('auth/login', [
        'erro' => 'Preencha e-mail e senha.'
      ]);

      return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

      View::render('auth/login', [
        'erro' => 'Informe um e-mail válido.'
      ]);

      return;
    }

    // =====================================================
    // Credenciais
    // (troque depois por banco de dados)
    // =====================================================

    $adminEmail = 'admin@admin.com';
    $adminSenha = '123456';

    if (
      $email !== $adminEmail ||
      $senha !== $adminSenha
    ) {

      View::render('auth/login', [
        'erro' => 'E-mail ou senha inválidos.'
      ]);

      return;
    }

    // =====================================================
    // Login OK
    // =====================================================

    session_regenerate_id(true);

    $_SESSION['auth'] = true;

    $_SESSION['user'] = [
      'email' => $email,
      'nome'  => 'Administrador',
    ];

    $this->redirect('/admin/viacoes');
  }

  /**
   * =========================================================
   * Logout
   * =========================================================
   */
  public function logout(): void
  {
    $this->startSession();

    $_SESSION = [];

    // Remove cookie da sessão
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

  /**
   * =========================================================
   * Verifica autenticação
   * =========================================================
   */
  public static function check(): bool
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    return isset($_SESSION['auth']) &&
      $_SESSION['auth'] === true;
  }

  /**
   * =========================================================
   * Retorna usuário logado
   * =========================================================
   */
  public static function user(): ?array
  {
    if (!self::check()) {
      return null;
    }

    return $_SESSION['user'] ?? null;
  }

  /**
   * =========================================================
   * Protege rotas privadas
   * =========================================================
   */
  public static function requireAuth(): void
  {
    if (!self::check()) {

      header('Location: /login');
      exit;
    }
  }

  /**
   * =========================================================
   * Inicia sessão
   * =========================================================
   */
  private function startSession(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
   * =========================================================
   * Verifica se está autenticado
   * =========================================================
   */
  private function isAuthenticated(): bool
  {
    return isset($_SESSION['auth']) &&
      $_SESSION['auth'] === true;
  }

  /**
   * =========================================================
   * Redirect helper
   * =========================================================
   */
  private function redirect(string $url): void
  {
    header("Location: {$url}");
    exit;
  }
}
