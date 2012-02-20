<?php

namespace LookupApp\Tests;

/**
 * Tests "Summon Query Links"
 */

 
class SummonQueryTest extends \PHPUnit_Framework_TestCase {
  protected function setUp() {
    $this->summon_base_url = "http://princeton.summon.serialssolutions.com/search?";
    $this->summon_query_param = "s.q=";
  }
  
  
  function testIsValidSummonQuery() {
    $query_strings = array("dogs", "dogs and cats", "Diwight Eisenhower");
    foreach($query_strings as $query) {
      $summon_query = new \PrimoServices\SummonQuery($query);
      $this->assertEquals($summon_query->getLink(), $this->summon_base_url . $this->summon_query_param . $query);
    }
  }
  
}

?>