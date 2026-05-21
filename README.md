Quero Passagem / Viações MVC
Descrição

Sistema web desenvolvido em PHP seguindo arquitetura MVC para gerenciamento de viações, usuários e histórico de alterações. O projeto inclui autenticação, controle administrativo, filtros de listagem, upload de logos e registro de histórico das modificações realizadas no sistema.

Funcionalidades
Login de usuários
Cadastro e gerenciamento de usuários
Cadastro, edição, exclusão e listagem de viações
Upload de logos das viações
Histórico de alterações (antes/depois) associado ao usuário responsável
Filtros nas listagens (viações, usuários e histórico)
Segurança com prepared statements e validações
Testes unitários
Tecnologias utilizadas
PHP
MySQL
HTML / CSS
Arquitetura MVC
Composer
PHPUnit

<img width="472" height="699" alt="image" src="https://github.com/user-attachments/assets/b02bd902-d83a-4fe8-8480-0aedd7d0af3b" />

docker:
<img width="900" height="590" alt="image" src="https://github.com/user-attachments/assets/cdbd61a3-8f64-454c-a3e9-b09a450c16f1" />
Segurança implementada
Prepared Statements em queries
Whitelist para ordenação dinâmica (ORDER BY)
Hash de senha com password_hash + PASSWORD_BCRYPT
Escape de saída com htmlspecialchars
Validação de dados no backend

Testes

vendor/bin/phpunit

Autor
Bruno Farias
