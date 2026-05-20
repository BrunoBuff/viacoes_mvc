<?php

declare(strict_types=1);

namespace App\Validators;

use App\Repositories\UsuarioRepository;
use Exception;

final class UsuarioValidator
{
  private UsuarioRepository $repository;

  public function __construct(?UsuarioRepository $repository = null)
  {
    // Reaproveita o repositório para verificar e-mails duplicados
    $this->repository = $repository ?? new UsuarioRepository();
  }

  /**
   * Valida os dados do usuário.
   * * @param array $data Dados vindos do $_POST
   * @param int|null $ignorarId ID do usuário a ser ignorado na checagem de e-mail (usado no update)
   * @throws Exception
   */
  public function validar(array $data, ?int $ignorarId = null): void
  {
    $errors = [];

    // 1. Validação do Nome
    $nome = trim((string)($data['nome'] ?? ''));
    if ($nome === '') {
      $errors[] = 'O campo nome é obrigatório.';
    } elseif (strlen($nome) < 3) {
      $errors[] = 'O nome deve ter pelo menos 3 caracteres.';
    }

    // 2. Validação do E-mail
    $email = trim((string)($data['email'] ?? ''));
    if ($email === '') {
      $errors[] = 'O campo e-mail é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'O e-mail informado não é válido.';
    } elseif ($this->repository->emailExiste($email, $ignorarId)) {
      $errors[] = 'Este e-mail já está sendo usado por outro usuário.';
    }

    // 3. Validação da Senha
    $password = (string)($data['password'] ?? '');

    // Se for um cadastro (ignorarId é null), a senha é obrigatória
    if ($ignorarId === null && $password === '') {
      $errors[] = 'O campo senha é obrigatório.';
    } // Se a senha foi preenchida (seja no cadastro ou na edição), valida o tamanho
    elseif ($password !== '' && strlen($password) < 6) {
      $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
    }

    // Se houver erros, junta todos com o separador pipe '|'
    if (!empty($errors)) {
      throw new Exception(implode('|', $errors));
    }
  }
}
