<?php

namespace Pudl;

use Pudl\Record as PudlRecord;
use Utilities\Parser as XmlParser;



class Parser
{
  private $records = array();
  //private $namespaces = array(
  //  "pulfa" => "http://library.princeton.edu/pudl",
//);

  function __construct($xml) {
      $dom = XmlParser::convertToDOMDocument($xml);
      $this->root_element = $dom->documentElement;
      $this->xpath = $this->loadXPath($dom);
      //foreach($this->namespaces as $prefix => $namespace) {
      //    $this->xpath->registerNamespace($prefix, $namespace);
      //}
      $this->loadRecords();
  }


  private function loadXPath($dom) {
    try {
        return new \DOMXPath($dom);
    } catch (Exception $e) {
        return "Error Parsing Pudl Search Results: " . $e->getMessage();
    }
    }

  private function query($path) {
    return $this->xpath->query($path);
    }

  private function loadRecords() {
    $records = $this->xpath->query("//Object");
    foreach($records as $record) {
        $record = new \Pudl\Record($record);
        array_push($this->records, $record->getRecordData());
        }
    }

  public function getRecords() {
    return $this->records;
    }

  public function getHits() {

    return $this->root_element->getAttribute("total");
    }

}
