<?php

namespace DiscoveryUtils\Tests;

/**
 *
 */
class PulfaTest extends \PHPUnit\Framework\TestCase {

  protected function setUp(): void {
    $pulfa_conf = array(
      'host' => "http://pulfa.princeton.edu",
      'base' => "/collections.xml?"
    );
    $this->pulfa = new \Pulfa\Pulfa($pulfa_conf['host'], $pulfa_conf['base']);
  }

  function testPulfaQuery() {
    $pulfa_response_data = $this->pulfa->query("woodrow wilson", 0, 10);
    $this->assertIsString($pulfa_response_data);

  }

}
