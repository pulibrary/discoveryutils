<?php
namespace LookupApp\Tests;
use Silex\WebTestCase;

/*
 * Test should pass if you are running code from machine
 * authorized to use Primo Web Services. Check back office to 
 * authorize a new machine. Must have fixed IP unfortunately. 
 */

class AuthClientTest extends WebTestCase
{
  public function createApplication() {
    $app = require __DIR__.'/../../../src/app.php';
    $app['debug'] = true;
    unset($app['exception_handler']);

    return $app;
  }
  
  public function testEmptyRecordResponse() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/record/PRN_VOYAGER5399326');
    $response = $client->getResponse();
    $response_code = $response->getStatusCode();
    $this->assertEquals(200, $response_code);
    $this->assertContains("No Source Record", $crawler->filter('body div p')->text());
  }
  
  public function testSucceedsFromAuthorizedServer() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/record/PRN_VOYAGER5399326');
    $response = $client->getResponse();
    $response_code = $response->getStatusCode();
    $this->assertEquals(200, $response_code);
  }
  
}
   