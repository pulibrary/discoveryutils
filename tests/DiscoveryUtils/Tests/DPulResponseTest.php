<?php

class DPulReponseTest extends \PHPUnit\Framework\TestCase {


  protected function setUp(): void {
    $this->dpul_response_json = file_get_contents(dirname(__FILE__).'/../../support/dpul_search_response.json');
    $query = "music";
    $this->response = \Blacklight\DpulResponse::getResponse($this->dpul_response_json, 'https://dpul.princeton.edu');
    $this->records = $this->response["records"];
  }

  function testCatalogResponseProvidesNumber() {
    $this->assertEquals($this->response["number"], 158);
  }

  function testCatalogReturnsRecords() {
    $this->assertTrue(isset($this->response["records"]));
    $this->assertEquals($this->records[0]["id"], "a558b450fa12cb8eab4a332bc3b235a6");
    $this->assertEquals($this->records[0]["title"], "[Cat near fishbowl].");
    $this->assertEquals($this->records[3]["contributor"], "Nast, Thomas, 1840-1902");
    $this->assertEquals($this->records[0]["type"], ["Visual material"]);
    $this->assertEquals($this->records[0]["url"], "https://dpul.princeton.edu/catalog/a558b450fa12cb8eab4a332bc3b235a6");
  }

}
