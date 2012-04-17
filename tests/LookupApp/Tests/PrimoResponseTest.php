<?php

namespace LookupApp\Tests;
//use PrimoServices\PrimoResponse;

class LookupPrimoResponseTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
      $primo_server_connection = array(
        'base_url' => 'http://searchit.princeton.edu',
        'institution' => 'PRN',
        'default_view_id' => 'PRINCETON',
        'default_pnx_source_id' => 'PRN_VOYAGER',
      );
      $dedup_record_response = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER4773991.xml');
      $this->single_dedup_response = new \PrimoServices\PrimoResponse($dedup_record_response, $primo_server_connection);
      $multiple_record_response = file_get_contents(dirname(__FILE__).'../../../support/multiple_results_set.xml');
      $this->multiple_response = new \PrimoServices\PrimoResponse($multiple_record_response, $primo_server_connection);
    }
    
    public function testGetHitCount() {
      $this->assertEquals(1, $this->single_dedup_response->getHits());
      $this->assertEquals(177, $this->multiple_response->getHits());
    }
    
    public function testGetResults() {
      $this->assertTrue($this->single_dedup_response->result_set[0] instanceof \PrimoServices\PrimoRecord);
      foreach($this->multiple_response->result_set as $record) {
        $this->assertTrue($record instanceof \PrimoServices\PrimoRecord);
      }
    }
}