<?php

namespace LookupApp\Tests;

class LookupPrimoParserTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $this->dedup_record_response = file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER4773991.xml');
    $this->simple_xml_doc = new \SimpleXMLElement(file_get_contents(dirname(__FILE__).'../../../support/single_voyager_source.xml')); 
    $this->dom_document = new \DOMDocument(); 
    $this->dom_document->loadXML(file_get_contents(dirname(__FILE__).'../../../support/PRN_VOYAGER857469.xml'));
    $this->primo_getit_response = new \DOMDocument();
    $this->primo_getit_response->loadXML(file_get_contents(dirname(__FILE__).'../../../support/getit-response.xml'));
  }
  
  function testConvertXMLStringToDOMDocument() {
    $this->assertInstanceOf('DOMDocument', \PrimoServices\PrimoParser::convertToDOMDocument($this->dedup_record_response));
  }
  
  function testConvertSimpleXMLDocumentToDOMDocument() {
    $this->assertInstanceOf('DOMDocument', \PrimoServices\PrimoParser::convertToDOMDocument($this->simple_xml_doc));
  }
  
  function testDOMDocumentReturnsDOMDocument() {
    $this->assertInstanceOf('DOMDocument', \PrimoServices\PrimoParser::convertToDOMDocument($this->dom_document));
  }
  
  function testDOMNodeReturnsDOMDocument() {
    $record_node_list = $this->primo_getit_response->getElementsByTagName('record');
    $dom_node_record = $record_node_list->item(0); //Take first record 
    $this->assertInstanceOf('DOMNode', $dom_node_record);
    $this->assertInstanceOf('DOMDocument', \PrimoServices\PrimoParser::convertToDOMDocument($dom_node_record));
  }
  
}