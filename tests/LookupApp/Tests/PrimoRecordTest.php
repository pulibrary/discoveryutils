<?php

namespace LookupApp\Tests;

/**
 * 
 */
class LookupPrimoRecordTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $single_record_reponse = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER4773991.xml');
    $this->single_source_record = new \PrimoServices\PrimoRecord($single_record_reponse);
    $dedup_record_reponse = file_get_contents(dirname(__FILE__).'../../../support/dedup_response.xml');
    $this->dedup_source_record = new \PrimoServices\PrimoRecord($dedup_record_reponse);
  }
  
  function testGetSinglePrintRecordLocations() {
    $this->assertInternalType('array', $this->single_source_record->getAvailableLibraries());
    //print_r($this->single_source_record->getAvailableLibraries());
    $this->assertEquals(1, count($this->single_source_record->getAvailableLibraries()));
    $this->assertArrayHasKey('PRN_VOYAGER4773991', $this->single_source_record->getAvailableLibraries());
  }
  
  function testIsFullTextOnlineResourceLink() {
    
  }
  
  function testIsFullTextSFXResourceLink() {
    
  }
  
  function testGetFullTextURL() {
    
  }
  
  function testGetFullTextUrlNotPresent() {
    
  }
  
  function testGetRisFields() { // see RIS mapping table in docs 
    $this->assertInternalType('string', $this->single_source_record->getCitation('RIS'));
  }
  
  function testGetPermaLink() {
    
  }
  
  function testGetAddData() {
    $this->assertInternalType('array', $this->single_source_record->getPrimoDocumentData());
  }
  
  function testGetRecordID() {
    $this->assertEquals('PRN_VOYAGER4773991', $this->single_source_record->getRecordID());
  }
}
