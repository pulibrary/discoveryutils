<?php

namespace LookupApp\Tests;

/**
 * 
 */
class PulfaTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $pulfa_conf = array(
      'host' => "http://findingaids.princeton.edu",
      'base' => "/collections.xml?"
    );
    $this->pulfa = new \Pulfa\Pulfa($pulfa_conf['host'], $pulfa_conf['base']);
  }
  
  function testPulfaQuery() {
    $pulfa_response_data = $this->pulfa->query("woodrow wilson", 0, 10);
    $this->assertInternalType('string', $pulfa_response_data);
    
  }
  
}