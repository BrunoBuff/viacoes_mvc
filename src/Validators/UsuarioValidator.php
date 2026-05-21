<?php
declare(strict_types=1);

namespace App\Validators;

use Exception;

/**
 * Valida dados de entrada do formulário de usuário.
 */
final class UsuarioValidator
{
  /**
   * Valida criação — senha obrigatória.
   * @throws Exception  Mensagens separadas por '|'.
   */
  public function validarCriacao(array $data): void
  {
    $errors = [];

    $nome     = trim((string) ($data['nome']     ?? ''));
    $email    = trim((string) ($data['email']    ?? ''));
    $password = trim((string) ($data['password'] ?? ''));

    if ($nome === '') {
      $errors[] = 'O campo nome é obrigatório.';
    } elseif (strlen($nome) < 3) {
      $errors[] = 'O nome deve ter pelo menos 3 caracteres.';
    }

    if ($email === '') {
      $errors[] = 'O campo e-mail é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'O e-mail informado não é válido.';
    }

    if ($password === '') {
      $errors[] = 'O campo senha é obrigatório.';
    } elseif (strlen($password) < 6) {
      $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
    }

    if (!empty($errors)) {
      throw new Exception(implode('|', $errors));
    }
  }

  /**
   * Valida edição — senha opcional, só validada se preenchida.
   * @throws Exception  Mensagens separadas por '|'.
   */
  public function validarEdicao(array $data): void
  {
    $errors = [];

    $nome     = trim((string) ($data['nome']     ?? ''));
    $email    = trim((string) ($data['email']    ?? ''));
    $password = trim((string) ($data['password'] ?? ''));

    if ($nome === '') {
      $errors[] = 'O campo nome é obrigatório.';
    } elseif (strlen($nome) < 3) {
      $errors[] = 'O nome deve ter pelo menos 3 caracteres.';
    }

    if ($email === '') {
      $errors[] = 'O campo e-mail é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'O e-mail informado não é válido.';
    }

    if ($password !== '' && strlen($password) < 6) {
      $errors[] = 'A nova senha deve ter pelo menos 6 caracteres.';
    }

    if (!empty($errors)) {
      throw new Exception(implode('|', $errors));
    }
  }
}
