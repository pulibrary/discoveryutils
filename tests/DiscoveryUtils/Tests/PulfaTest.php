<?php

namespace DiscoveryUtils\Tests;

/**
 *
 */
class PulfaTest extends \PHPUnit\Framework\TestCase {

  protected function setUp() {
    $pulfa_conf = array(
      'host' => "http://pulfa.princeton.edu",
      'base' => "/collections.xml?"
    );
    $this->pulfa = new \Pulfa\Pulfa($pulfa_conf['host'], $pulfa_conf['base']);
  }

  function testPulfaQuery() {
    $pulfa_response_data = $this->pulfa->query("woodrow wilson", 0, 10);
    $this->assertInternalType('string', $pulfa_response_data);

  }

}
