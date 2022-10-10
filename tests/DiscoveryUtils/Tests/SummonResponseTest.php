<?php

namespace DiscoveryUtils\Tests;

/**
 * Tests "Summon Query API"
 */

 
class SummonResponseTest extends \PHPUnit\Framework\TestCase  
{
  protected function setUp(): void {
    $this->summon_response_data = json_decode(file_get_contents(dirname(__FILE__).'../../../support/summon_response.json'), TRUE);
  }
  
  function testIsValidResponse() {
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );     
  }
  
}
