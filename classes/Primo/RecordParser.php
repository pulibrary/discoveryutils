<?php
namespace Primo;
use Primo\Record as PrimoRecord,
    Utilities\Parser as XmlParser;

class RecordParser {
  
  private $xpath;
  private $primo_server_connection;
  private $record_collection = array();
  private $namespaces = array(
    "sear" => "http://www.exlibrisgroup.com/xsd/jaguar/search",
    "def" => "http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib",
  );
  
  public function __construct($xml,$primo_server_connection) {
    $this->primo_server_connection = $primo_server_connection;
    $dom = XmlParser::convertToDOMDocument($xml);
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
      return "Error Parsing Primo Search Data: " . $e->getMessage();
    }
  }
  
  private function query($path) {
    return $this->xpath->query($path);
  }
  
  private function loadRecords() {
    $records = $this->xpath->query("//sear:DOC");
    foreach($records as $record) {
      $primo_record = new PrimoRecord($record, $this->primo_server_connection);
      array_push($this->record_collection, $primo_record);
    } 
  }
  
  public function getRecords() {
    return $this->record_collection;
  }
  
}
?>
