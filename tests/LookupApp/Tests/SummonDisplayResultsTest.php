<?php

namespace LookupApp\Tests;

use Silex\WebTestCase;

/**
 * Tests "Summon Query API"
 */

 
class SummonDisplayTest extends WebTestCase 
{
 
  public function createApplication() {
    $app = require __DIR__.'/../../../src/app.php';
    $app['debug'] = true;
    unset($app['exception_handler']);

    return $app;
  } 
  
}

