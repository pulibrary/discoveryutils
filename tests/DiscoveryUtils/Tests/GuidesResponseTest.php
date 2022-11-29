<?php

class GuidesReponseTest extends \PHPUnit\Framework\TestCase {

  protected function setUp(): void {
    $this->guide_response_json = file_get_contents(dirname(__FILE__).'../../../support/guide_search_response.json');
    $query = "cats";
    $this->response = new \Guides\Response(json_decode($this->guide_response_json), $query);
    $this->brief_response = $this->response->getBriefResponse();
  }

  function testGuideResponseProvidesMoreLink() {
    $this->assertTrue(isset($this->response->more_link));
    $this->assertStringContainsString('cats', $this->response->more_link);
  }

  function testGuideReturnsArrayOfRecords() {
    $this->assertIsArray($this->brief_response);
    $this->assertEquals(6, count($this->brief_response));
  }

  function testGuideReturnsOriginalQuery() {
    $this->assertTrue(isset($this->response->query));
    $this->assertEquals('cats', $this->response->query);
  }

}