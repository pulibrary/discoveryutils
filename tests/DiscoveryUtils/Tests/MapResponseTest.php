<?php

class MapReponseTest extends \PHPUnit\Framework\TestCase {


  protected function setUp(): void {
    $this->map_response_json = file_get_contents(dirname(__FILE__).'/../../support/map_search_response.json');
    $query = "cats";
    $this->response = \Blacklight\PulmapResponse::getResponse($this->map_response_json, 'https://maps.princeton.edu');
    $this->records = $this->response["records"];
  }

  function testMapResponseProvidesNumber() {
    $this->assertEquals($this->response["number"], 11);
  }

  function testMapReturnsRecords() {
    $this->assertTrue(isset($this->response["records"]));
    $this->assertEquals($this->records[0]["id"], "tufts-safricamn-pr-sa01");
    $this->assertMatchesRegularExpression('/This polygon dataset/', $this->records[0]["description"]);
    $this->assertEquals($this->records[0]["type"], "Shapefile");
    $this->assertEquals($this->records[0]["publisher"], "Statistics South Africa");
    $this->assertEquals($this->records[2]["author"],"United States Coast Survey");
  }

}