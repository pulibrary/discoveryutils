<?php
namespace DiscoveryUtils\Tests;
use Silex\WebTestCase;

class LookupTest extends WebTestCase
{
  public function createApplication() {
    $app = require __DIR__.'/../../../src/app.php';
    $app['debug'] = true;
    unset($app['exception_handler']);

    return $app;
  }

  public function testIndex() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/');
    $this->assertTrue($crawler->filter('html:contains("Discovery")')->count() > 0);
  }
 }
