<?php

class GuideReponseTest extends \PHPUnit_Framework_TestCase {

  protected function setUp() {
    $this->guide_response_json = file_get_contents(dirname(__FILE__).'../../../support/guide_search_response.json');
    $query = "cats";
    $this->response = new \Guides\Response(json_decode($this->guide_response_json), $query);
    $this->brief_response = $this->response->getBriefResponse();
  }

  function testGuideResponseProvidesMoreLink() {
    $this->assertTrue(isset($this->response->more_link));
    $this->assertContains('cats', $this->response->more_link);
  }

  function testGuideReturnsArrayOfRecords() {
    $this->assertInternalType('array', $this->brief_response);
    $this->assertEquals(6, count($this->brief_response));
  }

  function testGuideReturnsOriginalQuery() {
    $this->assertTrue(isset($this->response->query));
    $this->assertEquals('cats', $this->response->query);
  }

}