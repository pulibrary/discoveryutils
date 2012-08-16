<?php

namespace LookupApp\Tests;
use PrimoServices\PrimoRecord;


/*
 * Primo Holdings Test
 */
 
class PrimoHoldingTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $primo_server_connection = array(
      'base_url' => 'http://searchit.princeton.edu',
      'institution' => 'PRN',
      'default_view_id' => 'PRINCETON',
      'default_pnx_source_id' => 'PRN_VOYAGER',
    );
    $single_record_reponse = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER4773991.xml');
    $this->single_source_record = new \PrimoServices\PrimoRecord($single_record_reponse, $primo_server_connection);
    $dedup_record_reponse = file_get_contents(dirname(__FILE__).'../../../support/dedup_response.xml');
    $this->dedup_source_record = new \PrimoServices\PrimoRecord($dedup_record_reponse, $primo_server_connection);
    $electronic_record_response = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER5399326.xml');
    $this->electronic_record_response = new \PrimoServices\PrimoRecord($electronic_record_response,$primo_server_connection);
    $electronic_record_via_sfx_response = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER857469.xml');
    $this->electronic_record_via_sfx_response = new \PrimoServices\PrimoRecord($electronic_record_via_sfx_response,$primo_server_connection);
  }

  function testGetHoldings() {
    $holdings = $this->dedup_source_record->getHoldings();
    
    $this->assertEquals(3, count($holdings));
    foreach($holdings as $holding) {
      $this->assertInstanceOf('\PrimoServices\PrimoHolding', $holding);
    }
    
    $this->assertEquals('elf1', $holdings[0]->location_code);
    $this->assertEquals('FIRE', $holdings[1]->primo_library);
    $this->assertEquals('7500.503', $holdings[2]->call_number);
  } 
  
  function testGetPrimoLibraries() {
    $holdings = $this->dedup_source_record->getBriefHoldings();
    $this->assertEquals('ONLINE', $holdings[0]);
  }
  
}
