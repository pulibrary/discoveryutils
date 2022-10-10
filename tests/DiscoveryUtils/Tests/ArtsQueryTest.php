<?php

class ArtsQueryTest extends \PHPUnit\Framework\TestCase {

  protected function setUp(): void {
    $this->arts_response_json = file_get_contents(dirname(__FILE__).'/../../support/arts_search_response.json');
    $query = "cats";
    $art_query = new \Arts\Query(array('host' => 'https://data.artmuseum.princeton.edu', 'base' =>'/search'));
    $this->json_data = json_decode($art_query->query('gogh','artobjects'), true);
  }

  function testArtsResponseProvidesNumber() {
    $this->assertEquals($this->json_data["hits"]["total"], 68);
  }

  function testArtsReturnsRecords() {
    $this->assertEquals(count($this->json_data["hits"]["hits"]),5);
  }

}