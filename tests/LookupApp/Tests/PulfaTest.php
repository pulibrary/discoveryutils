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
    $this->pulfa_sample_response = file_get_contents(dirname(__FILE__).'../../../support/pulfaresponse.xml');
  }
  
  function testPulfaQuery() {
    $pulfa_response_data = $this->pulfa->query("woodrow wilson", 0, 10);
    $this->assertInternalType('string', $pulfa_response_data);
    $this->assertXmlStringEqualsXmlString($pulfa_response_data, $this->pulfa_sample_response);
  }
  
}