<?php

namespace LookupApp\Tests;

/**
 * 
 */
class PulfaRecordTest extends \PHPUnit_Framework_TestCase {
  protected function setUp() {
     $this->record_elements = array(
      "uri",
      "title",
    );
    $this->properties_that_donot_exist = array(
      "dogs",
      "cats",
    );
    $pulfa_parser = new \Pulfa\Parser(file_get_contents(dirname(__FILE__).'../../../support/findingaidsresult.xml'));
    $this->record_set = $pulfa_parser->getRecords();  
  }
  
  function testPulfaShowRecord() {
    $this->assertInstanceOf('\\Pulfa\\Record', $this->record_set[0]);
  }
  
  function testPulfaGetTitle() {
    $this->assertFalse(!$this->record_set[0]->title);
    $this->assertEquals('Papers of Woodrow Wilson Project Records', $this->record_set[0]->title);
  }
  
  function testPulfaGetUri() {
   foreach($this->record_set as $record) {
     $this->assertFalse(!$this->record_set[0]->uri);
   }
   $this->assertEquals('http://findingaidsbeta.princeton.edu/collections/AC237', $this->record_set[4]->uri);
  }
  
  function testHasNoDigitalContent() {
    $this->assertEquals('false', $this->record_set[0]->digital);
  }
  
  function testHasProperties() {
    $record = $this->record_set[9];
    foreach($this->record_elements as $element) {
      $this->assertTrue(isset($record->$element));
    }
  }
  
  function testDoesNotHaveProperties() {
    $record = $this->record_set[9];
    foreach($this->properties_that_donot_exist as $element) {
      $this->assertFalse(isset($record->$element));
    }
  }
  
}