<?php

namespace LookupApp\Tests;

/**
 * Tests "Summon Records Test"
 */

 
class SummonRecordTest extends \PHPUnit_Framework_TestCase  
{
  protected function setUp() {
    $this->summon_connection = array( //find a way to pass this in automatically?
      'client.id' => "princeton",
      'authcode' => 'LOIYKyKZbRiV0OVu9+worZW4ah'
    );
    $this->summon_client = new \Summon\Summon($this->summon_connection['client.id'], $this->summon_connection['authcode']);
    
  }
  
  public function testGetFormatType() {
    
  }
  
  public function testGetOpenURL() {
    
  }
  
  public function getGetFullTextLink() {
    
  }
  
}