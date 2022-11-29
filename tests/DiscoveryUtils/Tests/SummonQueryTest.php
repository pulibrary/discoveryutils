<?php

namespace DiscoveryUtils\Tests;

/**
 * Tests "Summon Query Links"
 */

 
class SummonQueryTest extends \PHPUnit\Framework\TestCase {
  protected function setUp(): void {
    $this->summon_base_url = "https://princeton.summon.serialssolutions.com/search?";
    $this->summon_query_param = "s.q=";
    
  }
  
  
  function testIsValidSummonQuery() {
    $query_strings = array("dogs", "dogs and cats", "Diwight Eisenhower", "(cats or dogs) and cartoons", "\"a good man is hard to find\"");
    foreach($query_strings as $query) {
      $summon_query = new \Summon\Query($query);
      $this->assertEquals($summon_query->getLink(), $this->summon_base_url . $this->summon_query_param . urlencode($query));
    }
  }
  
}

?>