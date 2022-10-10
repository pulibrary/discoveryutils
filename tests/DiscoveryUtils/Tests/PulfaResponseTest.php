<?php

namespace DiscoveryUtils\Tests;

/**
 * 
 */
class PulfaResponseTest extends \PHPUnit\Framework\TestCase {
  
  protected function setUp(): void {
    $this->xml_response_data = file_get_contents(dirname(__FILE__).'../../../support/findingaidsresult.xml');
    $this->sample_query = "woodrow+wilson";
  }
  
  function testParseResponse() {
    $response = new \Pulfa\Response($this->xml_response_data, $this->sample_query);
    $this->assertInstanceOf('\\Pulfa\\Response', $response);
  }
  
  
  function testGetBriefResponse() {
    $response = new \Pulfa\Response($this->xml_response_data, $this->sample_query);
    $brief_response = $response->getBriefResponse();
    $this->assertIsArray($brief_response);
    $this->assertArrayHasKey('records', $brief_response);
    $this->assertArrayHasKey('more', $brief_response);
    $this->assertEquals(10, count($brief_response['records']));
    $this->assertEquals('James Kerney Collection on Woodrow Wilson', $brief_response['records'][8]['title']);
  }
  
  
}