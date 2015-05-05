<?php


namespace DiscoveryUtils\Tests;
use Primo\Record;
use Symfony\Component\Yaml\Yaml;

/*
 * Primo Holdings Test
 */
 
class PrimoArchivalItemsTest extends \PHPUnit_Framework_TestCase {
  
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
    $this->single_item_list = $this->single_archival_holding_record->getArchivalItems();
    $many_archival_holding_response = file_get_contents(dirname(__FILE__).'../../../support/XMLC0101_c0.xml');
    $this->many_archival_holding_record = new \Primo\Record($many_archival_holding_response, $primo_server_connection);
    $this->many_item_list = $this->many_archival_holding_record->getArchivalItems();
    $visuals_record = file_get_contents(dirname(__FILE__).'../../../support/Visuals509.xml');
    $this->visuals_record = new \Primo\Record($visuals_record,$primo_server_connection);
    $this->visuals_item_list = $this->visuals_record->getArchivalItems();
  }

  function testAllItemsHaveLocationCodes() {
    foreach($this->many_item_list as $item) {
      $this->assertInternalType('string', $item->location_code);
    }
  }

  function testAllItemsHaveBoxNumbers() {
    foreach($this->many_item_list as $item) {
      $this->assertInternalType('string', $item->box_number);
    }
  }
  
  function testAllItemsHaveCallNumbers() {
    foreach($this->many_item_list as $item) {
      $this->assertInternalType('string', $item->call_number);
    }
  }
  
  function testItemsWihSeriesDescriptions() {
    foreach($this->many_item_list as $item) {
      $this->assertInternalType('string', $item->series_details);
    }
  }

  function testItemsWithoutSeriesDescriptions() {
    foreach($this->single_item_list as $item) {
      $this->assertFalse(isset($item->series_details));
    }
  }

  function testItemWithoutEnhancedTitle() {
    $this->assertNotContains('F. Scott Fitzgerald Subject File (correspondence)', $this->single_item_list[0]->request_url);
  }

  function testItemWithEnhancedTitle() {
    $this->assertContains('Painting', $this->visuals_item_list[0]->request_url);
  }
}