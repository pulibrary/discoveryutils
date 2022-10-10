<?php

namespace DiscoveryUtils\Tests;

/**
 * 
 */
class PulfaParserTest extends \PHPUnit\Framework\TestCase {
    
  protected function setUp(): void {
    $this->pulfa_response = file_get_contents(dirname(__FILE__).'../../../support/findingaidsresult.xml');
    $this->pulfa_parser = new \Pulfa\Parser($this->pulfa_response);
  }
  
  function testPulfaBasicParse() {

    $records = $this->pulfa_parser->getRecords();
    $this->assertIsArray($records);
    foreach($records as $record) {
      $this->assertInstanceOf('\\Pulfa\\Record', $record);
    }
  }
  
  function testPulfaNumberOfRecords() {
    $records = $this->pulfa_parser->getRecords();
    $this->assertEquals(10, count($records));
  }
  
  function testPulfaHits() {
    $this->assertEquals($this->pulfa_parser->getHits(), 13026);
  }
  
  function testPulfaGetURI() {
    $this->assertEquals($this->pulfa_parser->getQueryURI(), "http://findingaidsbeta.princeton.edu/collections?v1=woodrow wilson&f1=kw&b1=AND&v2=&f2=kw&b2=AND&v3=&f3=kw&year=before&ed=&ld=&start=0&rpp=10");
  }
   
   
}