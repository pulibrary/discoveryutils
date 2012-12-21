<?php

namespace LookupApp\Tests;

/**
 * Tests "Summon Records Test"
 */

 
class SummonRecordTest extends \PHPUnit_Framework_TestCase  
{
  protected function setUp() {
    $this->summon_response_data = json_decode(file_get_contents(dirname(__FILE__).'../../../support/summon_response.json'), TRUE);
    $this->summon_response = new \Summon\Response($this->summon_response_data);
    $this->records = $this->summon_response->getBriefResults();
  }
  
  public function testIsSummonRecords() {
    foreach($this->records as $record) {
      $this->assertInstanceOf('\\Summon\\Record', $record);
    }
  }
  
  public function testGetFormatType() {
    
  }
  
  public function testGetOpenURL() {
    
  }
  
  
  
}