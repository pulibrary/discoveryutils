<?php

namespace LookupApp\Tests;
use Primo\Record;
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

    $single_record_reponse = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER4773991.xml');
    $this->single_source_record = new \Primo\Record($single_record_reponse, $primo_server_connection);
    $dedup_record_reponse = file_get_contents(dirname(__FILE__).'../../../support/dedup_response.xml');
    $this->dedup_source_record = new \Primo\Record($dedup_record_reponse, $primo_server_connection);
    $electronic_record_response = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER5399326.xml');
    $this->electronic_record_response = new \Primo\Record($electronic_record_response,$primo_server_connection);
    $electronic_record_via_sfx_response = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER857469.xml');
    $this->electronic_record_via_sfx_response = new \Primo\Record($electronic_record_via_sfx_response,$primo_server_connection);
    $with_rare_books_dedup_record_reponse = file_get_contents(dirname(__FILE__).'../../../support/dedupmrg34322777.xml');
    $this->with_rare_dedup_source_record = new \Primo\Record($with_rare_books_dedup_record_reponse, $primo_server_connection);
    
  }

  function testGetHoldings() {
    $holdings = $this->dedup_source_record->getHoldings();
    
    $this->assertEquals(3, count($holdings));
    foreach($holdings as $holding) {
      $this->assertInstanceOf('\Primo\Holdings\Holding', $holding);
    }
    
    $this->assertEquals('elf1', $holdings[0]->location_code);
    $this->assertEquals('FIRE', $holdings[1]->primo_library);
    $this->assertEquals('7500.503', $holdings[2]->call_number);
  } 
  
  function testGetPrimoLibraries() {
    $holdings = $this->dedup_source_record->getBriefHoldings();
    $this->assertEquals('ONLINE', $holdings[0]);
  }

  function testGetHoldingsWithRareBooksLocations() {
    $holdings = $this->with_rare_dedup_source_record->getHoldings();
    $brief_holdings = $this->with_rare_dedup_source_record->getBriefHoldings();
    $this->assertEquals(5, count($holdings));
    $this->assertEquals(5, count($brief_holdings));
    
  }
  
}
