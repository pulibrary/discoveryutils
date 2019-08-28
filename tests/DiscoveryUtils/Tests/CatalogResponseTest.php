<?php

class CatalogReponseTest extends \PHPUnit\Framework\TestCase {


  protected function setUp() {
    $this->catalog_response_json = file_get_contents(dirname(__FILE__).'/../../support/catalog_search_response.json');
    $query = "cats";
    $this->response = \Blacklight\Response::getResponse($this->catalog_response_json, 'https://catalog.princeton.edu');
    $this->records = $this->response["records"];
  }

  function testCatalogResponseProvidesNumber() {
    $this->assertEquals($this->response["number"], 14559567);
  }

  function testCatalogReturnsRecords() {
    $this->assertTrue(isset($this->response["records"]));
    $this->assertEquals($this->records[0]["id"], "11407761");
    $this->assertEquals($this->records[0]["type"], ["Book"]);
    $this->assertEquals($this->records[3]["publisher"], ["Dinar: Dinar Belediyesi,"]);
    $this->assertEquals($this->records[0]["author"], ["Peker, Nurettin"]);
  }

}
