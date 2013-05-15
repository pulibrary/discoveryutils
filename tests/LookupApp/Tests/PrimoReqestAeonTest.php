<?php

namespace LookupApp\Tests;
use Symfony\Component\Yaml\Yaml;

class PrimoRequestAeonTest extends \PHPUnit_Framework_TestCase 
{
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
  }

  function testCreateRequest() {
    $record_to_request = $this->single_archival_holding_record;
    $this->assertInstanceOf('\\Primo\\Record', $record_to_request);
    $item_to_request = $this->single_item_list[0];
    $this->assertInstanceOf('\\Primo\\Items\\Archives', $item_to_request);
    $holding_to_request = $this->single_archival_holding_record->getArchivalHoldings();
    $this->assertInstanceOf('\\Primo\\Holdings\\Archives', $holding_to_request);
    $request = \Primo\Requests\Aeon::createRequest($record_to_request, $holding_to_request, $item_to_request);
    $this->assertInternalType('array', $request);
    // Now form the request 
  }
  
  function testHasRequiredAeonParams() {
    
  }
  
  function testHasOptionalAeonParams() {
    
  }
}
