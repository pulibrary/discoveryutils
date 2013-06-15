<?php

namespace LookupApp\Tests;

/**
 * 
 */
class PudlResponseTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $this->xml_response_data = file_get_contents(dirname(__FILE__).'../../../support/pudlsearchresponse.xml');
    $this->sample_query = "woodrow+wilson";
  }
  
  function testParseResponse() {
    $response = new \Pudl\Response($this->xml_response_data, $this->sample_query);
    $this->assertInstanceOf('\\Pudl\\Response', $response);
  }
  
  
  function testGetBriefResponse() {
    $response = new \Pudl\Response($this->xml_response_data, $this->sample_query);
    $brief_response = $response->getBriefResponse();
    $this->assertInternalType('array', $brief_response);
    $this->assertArrayHasKey('number', $brief_response);
    $this->assertEquals(7, $brief_response['number']);
    $this->assertArrayHasKey('more', $brief_response);
    $this->assertEquals(3, count($brief_response['records']));
    $this->assertArrayHasKey('records', $brief_response);
    $this->assertArrayHasKey('title', $brief_response['records'][0]);
    //print_r($brief_response);
    $this->assertEquals('Biblia Latina', $brief_response['records'][0]['title']);
    $this->assertEquals('7d278t10z', $brief_response['records'][0]['id']);
  }
  
  
}