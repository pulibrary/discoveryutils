<?php

namespace LookupApp\Tests;

use Silex\WebTestCase;

/**
 * Tests "Summon Query API"
 */

 
class SummonQueryDisplayResultsTest extends \WebTestCasee 
{
 
  public function createApplication() {
    $app = require __DIR__.'/../../../src/app.php';
    $app['debug'] = true;
    unset($app['exception_handler']);

    return $app;
  } 
  
  public function testGetResults() {
    
  }
  
  public function testGetFormatType() {
    
  }
  
  public function testGetOpenURL() {
    
  }
  
  public function getGetFullTextLink() {
    
  }
  
}

