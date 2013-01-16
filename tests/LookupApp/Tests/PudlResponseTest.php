<?php

namespace LookupApp\Tests;

/**
 * 
 */
class PudlResponseTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $this->xml_response_data = file_get_contents(dirname(__FILE__).'../../../support/pudlsearchresponse.xml');
  }
  
  function testParseResponse() {
    $response = new \Pudl\Response($this->xml_response_data);
    $this->assertInstanceOf('\\Pudl\\Response', $response);
  }
  
  
  function testGetBriefResponse() {
    $response = new \Pudl\Response($this->xml_response_data);
    $brief_response = $response->getBriefResponse();
    $this->assertInternalType('array', $brief_response);
    $this->assertArrayHasKey('hits', $brief_response);
    $this->assertEquals(7, $brief_response['hits']);
    $this->assertArrayHasKey('records', $brief_response);
    //$this->assertArrayHasKey('more', $brief_response);
    //$this->assertEquals(10, count($brief_response['records']));
    //$this->assertEquals('James Kerney Collection on Woodrow Wilson', $brief_response['records'][8]['title']);
  }
  
  
}