<?php

class ArtsReponseTest extends \PHPUnit\Framework\TestCase {

  protected function setUp() {
    $this->arts_response_json = file_get_contents(dirname(__FILE__).'/../../support/arts_search_response.json');
    $query = "cats";
    $this->response = \Arts\Response::getResponse($this->arts_response_json, 'https://artmuseum.princeton.edu');
    $this->records = $this->response["records"];
  }

  function testArtsResponseProvidesNumber() {
    $this->assertEquals($this->response["number"], 68);
  }

  function testArtsReturnsRecords() {
    $this->assertTrue(isset($this->response["records"]));
  }

}