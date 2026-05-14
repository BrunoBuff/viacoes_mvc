<?php
declare(strict_types=1);
namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use App\Core\Router;

class RouterTest extends TestCase
{
  public function testRotaGetEResolvida(): void
  {
    $router  = new Router();
    $chamado = false;

    $router->get('/admin/viacoes', function () use (&$chamado) {
      $chamado = true;
    });

    $router->dispatch('GET', '/admin/viacoes');
    $this->assertTrue($chamado);
  }

  public function testMethodSpoofingConvertePOSTEmPUT(): void
  {
    $_POST['_method'] = 'PUT';
    $chamado = false;

    $router = new Router();
    $router->put('/admin/viacoes/1', function () use (&$chamado) {
      $chamado = true;
    });

    $router->dispatch('POST', '/admin/viacoes/1');
    $this->assertTrue($chamado);

    unset($_POST['_method']);
  }

  public function testMethodSpoofingConvertePOSTEmDELETE(): void
  {
    $_POST['_method'] = 'DELETE';
    $chamado = false;

    $router = new Router();
    $router->delete('/admin/viacoes/1', function () use (&$chamado) {
      $chamado = true;
    });

    $router->dispatch('POST', '/admin/viacoes/1');
    $this->assertTrue($chamado);

    unset($_POST['_method']);
  }
}
