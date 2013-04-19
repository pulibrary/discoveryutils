<?php


namespace LookupApp\Tests;
use Primo\Record;
use Primo\FindingAidHolding;
use Symfony\Component\Yaml\Yaml;

/*
 * Primo Holdings Test
 */
 
class PrimoHoldingTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $library_scopes = Yaml::parse(file_get_contents(dirname(__FILE__).'../../../support/scopes.yml'));
    $primo_server_connection = array(
      'base_url' => 'http://searchit.princeton.edu',
      'institution' => 'PRN',
      'default_view_id' => 'PRINCETON',
      'default_pnx_source_id' => 'PRN_VOYAGER',
      'available.scopes' => $library_scopes,
      'record.request.base' => "http://library.princeton.edu/requests",
    );
    
    $single_archival_holding_response = file_get_contents(dirname(__FILE__).'../../../support/XMLC0751_c004.xml');
    $this->single_archival_holding_record = new \Primo\Record($single_archival_holding_response, $primo_server_connection);
    $many_archival_holding_response = file_get_contents(dirname(__FILE__).'../../../support/XMLC0101_c0.xml');
    $this->many_archival_holding_record = new \Primo\Record($many_archival_holding_response, $primo_server_connection);
    
  }

  function testIsArchives() {
    $this->assertEquals("archives", $this->single_archival_holding_record->getFormatType());
  }

  function testHasArchivalHoldings() {
    $this->assertTrue($this->single_archival_holding_record->isA("archives"));
    $this->assertTrue($this->single_archival_holding_record->isXMLSource());
  }
  
  function testIsOtherSourceSystem() {
    $this->assertEquals("Other", $this->single_archival_holding_record->getSourceSystem());
  }
  
  function testNumberOfArchivalHoldings() {
    
  }
  
  function testArchivalHoldingHasLocationCode() {
    
  }
  
  function testArchivalHoldingHasCallNumber() {
    
  }

}