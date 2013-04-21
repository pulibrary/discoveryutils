<?php
namespace LookupApp\Tests;
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
   
  public function testResultsJson() {
    $client = $this->createClient();
    $client->request('GET', '/record/PRN_VOYAGER6109368');
    $json_data = $client->getResponse()->getContent();
    $this->assertContains("PRN_VOYAGER6109368", $json_data); // not a good test html_data is sent back not json data does contain the string though 
  }
  
  public function testSinglePrimoRecordResponse() {
    
  }
  
  public function testCompositePrimoRecordResponse() {
    
  }
  
}
