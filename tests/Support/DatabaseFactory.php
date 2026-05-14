<?php
declare(strict_types=1);
namespace Tests\Support;

use PDO;

final class DatabaseFactory
{
  public static function make(): PDO
  {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec("
            CREATE TABLE viacoes (
                id        INTEGER PRIMARY KEY AUTOINCREMENT,
                nome      TEXT NOT NULL,
                url       TEXT NOT NULL,
                cidade    TEXT NOT NULL,
                status    TEXT DEFAULT 'ativo',
                logo      TEXT DEFAULT NULL,
                criado_em TEXT DEFAULT (datetime('now')),
                alterado_em TEXT DEFAULT (datetime('now'))
            )
        ");

    return $pdo;
  }
}
