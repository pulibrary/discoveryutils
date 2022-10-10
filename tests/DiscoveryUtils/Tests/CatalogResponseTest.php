<?php

class CatalogReponseTest extends \PHPUnit\Framework\TestCase {


  protected function setUp(): void {
    $this->catalog_response_json = file_get_contents(dirname(__FILE__).'/../../support/catalog_search_response.json');
    $query = "cats";
    $this->response = \Blacklight\Response::getResponse($this->catalog_response_json, 'https://catalog.princeton.edu');
    $this->records = $this->response["records"];
  }

  function testCatalogResponseProvidesNumber() {
    $this->assertEquals($this->response["number"], 688);
  }

  function testCatalogReturnsRecords() {
    $this->assertTrue(isset($this->response["records"]));
    $this->assertEquals($this->records[0]["id"], "9974234393506421");
    $this->assertEquals($this->records[0]["type"], "Book");
    $this->assertEquals($this->records[3]["publisher"], ["La Jolla : Museum of Contemporary Art San Diego, 2006."]);
    $this->assertEquals($this->records[0]["author"], ["Kastner, Ruth E., 1955-"]);
  }

}
