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
  
  function testGetDedupRecordLocations() {
    $this->assertInternalType('array', $this->dedup_source_record->getAvailableLibraries());
    //print_r($this->single_source_record->getAvailableLibraries());
    $this->assertEquals(2, count($this->dedup_source_record->getAvailableLibraries()));
    $dedup_source_rec_ids = array("PRN_VOYAGER6610786", "PRN_VOYAGER490930");
    foreach($dedup_source_rec_ids as $source_rec_id) {
      $this->assertArrayHasKey($source_rec_id, $this->dedup_source_record->getAvailableLibraries());
    }
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
    $this->assertInternalType('string', $this->dedup_source_record->getCitation('RIS'));
  }
  
  function testGetPermaLink() {
    
  }
  
  function testGetAddData() {
    $this->assertInternalType('array', $this->single_source_record->getPrimoDocumentData());
    $this->assertInternalType('array', $this->dedup_source_record->getPrimoDocumentData());
  }
  
  function testGetRecordID() {
    $this->assertEquals('PRN_VOYAGER4773991', $this->single_source_record->getRecordID());
    $this->assertEquals('dedupmrg48669359', $this->dedup_source_record->getRecordID());
  }
}
