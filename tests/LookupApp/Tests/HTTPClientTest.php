<?php
namespace LookupApp\Tests;

use Guzzle\Service\Client;

/*
 * Test should pass if you are running code from machine
 * authorized to use Primo Web Services. Check back office to 
 * authorize a new machine. Must have fixed IP unfortunately. 
 */

class HTTPClientTest extends  \PHPUnit_Framework_TestCase
{
    protected function setUp() {
       $this->client = new \Guzzle\Service\Client('http://libwebprod.princeton.edu/searchit');
    }
    
    function testBaseURLSetting() {
      $response = $this->client->get('find/any/cats?limit=exact')->send();
      //echo $response->getBody();
    }
}
