<?php
namespace LookupApp\Tests;
use Silex\WebTestCase;

class AuthClientTest extends WebTestCase
{
  public function createApplication() {
    $app = require __DIR__.'/../../../src/app.php';
    $app['debug'] = true;
    unset($app['exception_handler']);

    return $app;
  }
  
    
  public function testFailsFromUnauthorizedServer() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/record/PRN_VOYAGER5399326');
  }
  
  public function testSucceedsFromAuthorizedServer() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/record/PRN_VOYAGER5399326');
    
  }
  
}
   