<?php
namespace PrimoServices;
use PrimoServices\PrimoRecordParser,
    PrimoServices\PrimoRecord;

class PrimoResponse
{
  
  private $type;
  private $hits;
  private $lasthit;
  private $firsthit;
  private $bulk_size; // this should have a defult 
  private $primo_server_connection;
  public $result_set;
  private $namespaces = array(
    "sear" => "http://www.exlibrisgroup.com/xsd/jaguar/search",
    "def" => "http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib",
  );
  function __construct($xml, $primo_server_connection) {
    $this->primo_server_connection = $primo_server_connection;
    $dom = PrimoParser::convertToDOMDocument($xml);
    $this->dom = $dom;
    $this->setHits();
    $this->buildResultSet();
  }
  
  public function getHits() {
    return $this->hits;
  }
  
  private function buildResultSet() {
    $primo_record_parser = new PrimoRecordParser($this->dom, $this->primo_server_connection);
    $this->result_set = $primo_record_parser->getRecords();
  }
  
  private function buildFacetSet() {
    
  }
  
  private function setHits() {
    $docset = $this->dom->getElementsByTagName("DOCSET")->item(0);
    $this->hits = $docset->getAttribute("TOTALHITS");
  }
  
  public function getResults() {
    return $this->result_set;
  }
  
  public function getBriefResults() {
    $brief_result_set = array();
    foreach($this->result_set as $primo_record) {
      $brief_result = array(
        'url' => $primo_record->getResourceLink(),
        'title' => trim($primo_record->getTitle()),
      );
      array_push($brief_result_set, $brief_result);
    }
    
    return $brief_result_set;
  }
  
  public function getFacets() {
    
  }
  
}