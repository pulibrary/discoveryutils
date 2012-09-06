<?php

namespace Pulfa;
use PrimoServices\PrimoParser as XmlParser;

class Parser 
{
  
  private $records = array();
  private $namespaces = array(
    "pulfa" => "http://library.princeton.edu/pulfa",
  );
  
  function __construct($xml) {
    $dom = XmlParser::convertToDOMDocument($xml);
    $this->root_element = $dom->documentElement;
    $this->xpath = $this->loadXPath($dom);
    foreach($this->namespaces as $prefix => $namespace) {
      $this->xpath->registerNamespace($prefix, $namespace);
    }  
    $this->loadRecords();  
  }
  
  
  private function loadXPath($dom) {
    try {
      return new \DOMXPath($dom);
    } catch (Exception $e) {
      return "Error Parsing Pulfa Search Results: " . $e->getMessage();
    }
  }
  
  private function query($path) {
    return $this->xpath->query($path);
  }
  
  private function loadRecords() {
    $records = $this->xpath->query("//pulfa:result");
    foreach($records as $record) {
      $record = new \Pulfa\Record($record);
      array_push($this->records, $record);
    } 
  }
  
  private function loadParams() {
    
  }
  
  public function getRecords() {
    return $this->records;
  }
  
  public function getHits() {
    
    return $this->root_element->getAttribute("total");
  }
  
  public function getQueryURI() {
    return $this->root_element->getAttribute("self"); 
  }
  
   
}
