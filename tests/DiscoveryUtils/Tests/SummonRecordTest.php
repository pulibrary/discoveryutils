<?php

namespace DiscoveryUtils\Tests;

/**
 * Tests "Summon Records Test"
 */

 
class SummonRecordTest extends \PHPUnit\Framework\TestCase  
{
  protected function setUp(): void {
    $this->summon_response_data = json_decode(file_get_contents(dirname(__FILE__).'../../../support/summon_response.json'), TRUE);
    $this->summon_response = new \Summon\Response($this->summon_response_data);
    $this->records = $this->summon_response->getBriefResults();
  }
  
  public function testIsSummonRecords() {
    foreach($this->records as $record) {
      $this->assertIsArray($record);
    }
  }
  
  public function testGetFormatType() {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );  
  }
  
  public function testGetOpenURL() {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );
  }
  
  
  
}
