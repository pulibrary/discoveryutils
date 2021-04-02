<?php

class PulfalightReponseTest extends \PHPUnit\Framework\TestCase {


  protected function setUp() {
    $this->pulfalight_search_response_json = file_get_contents(dirname(__FILE__).'/../../support/catalog_search_response.json');
    $this->response = \Blacklight\Response::getResponse($this->pulfalight_search_response_json, 'https://findingaids.princeton.edu');
    $this->records = $this->response["records"];
  }

  function testPulfalightReponseProvidesNumber() {
    $this->assertEquals($this->response["total_count"], 4052);
  }

  function testCatalogReturnsRecords() {
    $this->assertTrue(isset($this->response["records"]));
    $this->assertEquals($this->records[0]["id"], "AC412");
    $this->assertEquals($this->records[0]["type"][0], ["collection"]);
    $this->assertEquals($this->records[0]["repository"], ["Princeton University Archives"]);
    $this->assertEquals($this->records[0]["collection"], ["Princeton Nassoons Records, 1941-2012"]);
    $this->assertEquals($this->records[0]["dates"], ["1941-2012"]);
  }

}
