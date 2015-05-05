<?php

namespace DiscoveryUtils\Tests;

use Silex\WebTestCase;

/**
 * Tests "Summon Query API"
 */

 
class QueryClientResultsTest extends WebTestCase 
{
 
  public function createApplication() {
    $app = require __DIR__.'/../../../src/app.php';
    $app['debug'] = true;
    unset($app['exception_handler']);

    return $app;
  } 
  
  public function testBasicSummonSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/articles/any?query=music');
    $this->assertTrue($client->getResponse()->isOk());
  }
 
 public function testBasicPulfaSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/pulfa/any?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }
 
 public function testBasicPrimoSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/find/any?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }
  
 public function testEmptyQuery() {
   $client = $this->createClient();
   $crawler = $client->request('GET', '/find/any?query=');
   $this->assertTrue($client->getResponse()->isOk());
 } 
 
 public function testNoQueryParam() {
   $client = $this->createClient();
   $crawler = $client->request('GET', '/find/any');
   $this->assertTrue($client->getResponse()->isOk());
 } 
 
}

