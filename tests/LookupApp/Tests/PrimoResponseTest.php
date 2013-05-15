<?php

namespace LookupApp\Tests;

class LookupPrimoResponseTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
      $app = require dirname(__FILE__) . '/../../../src/app.php';
      $dedup_record_response = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER4773991.xml');
      $this->single_dedup_response = new \Primo\Response($dedup_record_response, $app['primo_server_connection']);
      $multiple_record_response = file_get_contents(dirname(__FILE__).'../../../support/multiple_results_set.xml');
      $this->multiple_response = new \Primo\Response($multiple_record_response, $app['primo_server_connection']);
    }
    
    public function testGetHitCount() {
      $this->assertEquals(1, $this->single_dedup_response->getHits());
      $this->assertEquals(177, $this->multiple_response->getHits());
    }
    
    public function testGetResults() {
      $this->assertTrue($this->single_dedup_response->result_set[0] instanceof \Primo\Record);
      foreach($this->multiple_response->result_set as $record) {
        $this->assertTrue($record instanceof \Primo\Record);
      }
    }
    
    public function iterateOverCurrentResponseResults() {
      $this->assertCount(10, count($this->multiple_response->getResults()));
      
    }
    
    public function testGetBriefResults() {
      $brief_result_set = $this->multiple_response->getBriefResults();
      //print_r($this->multiple_response->getBriefResults());
      $this->assertInternalType('array', $brief_result_set);
      $this->assertStringStartsWith('One fine potion', $brief_result_set[9]['title']); //check title in last mock object item
    }
    
    public function testGetHoldings() {
      $brief_result_set = $this->multiple_response->getBriefResults();
      $this->assertInternalType('array', $brief_result_set[0]['holdings']);
      $this->assertGreaterThanOrEqual(1, count($brief_result_set[0]['holdings']));
    }
}