<?php

namespace Pudl;
use Pudl\Parser as PudlParser;

class Response
{
  private $records;
  private $response_uri;
  private $hits;
  private $more = "http://pudl.princeton.edu/results.php?f1=kw&v1=";
  private $query;
  
  
  function __construct($xml, $query) {

    $pudl_parser = new PudlParser($xml);
    $this->query = $query;
    $this->hits = $pudl_parser->getHits();
    $this->records = $pudl_parser->getRecords();
    
  } 
  
  public function getBriefResponse() {
    $response = array();
    $response['number'] = $this->hits;
    $response['more'] = $this->more . $this->query;
    // get a slice of records 
    if($this->hits > 3) {
      $brief_record_list = array_slice($this->records, 0, 3); //FIXME should come from conf settings. Return only 3 for now
    } else {
      $brief_record_list = $this->records;
    }
    $response['records'] = $brief_record_list;
    return $response;
  }
  
  
}