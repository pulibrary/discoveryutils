<?php
namespace Primo;
use Primo\RecordParser as PrimoRecordParser,
    Primo\PrimoRecord as PrimoRecord,
    Primo\Parser as XmlParser;

class Response
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
    $dom = XmlParser::convertToDOMDocument($xml);
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
        'pnx_id' => $primo_record->getRecordID(),
        'url' => $primo_record->getResourceLink(),
        'fulltextavail' => $primo_record->hasFullText(),
        'full_text_link' => $primo_record->getFullTextLinktoSrc(),
        'title' => trim($primo_record->getTitle()),
        'holdings' => $primo_record->getBriefHoldings(),
        'format' => $primo_record->getFormatType(),
        'creationdate' => $primo_record->getCreationDate(),
      );
      array_push($brief_result_set, $brief_result);
    }
    
    return $brief_result_set;
  }
  
  public function getFacets() {
    
  }
  
}