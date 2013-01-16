<?php

namespace Pudl;
use Pudl\Parser as PudlParser;

class Response
{
  private $records;
  private $response_uri;
  private $hits;
  
  function __construct($xml) {
    
    $pudl_parser = new PudlParser($xml);
    $this->hits = $pudl_parser->getHits();
    $this->records = $pudl_parser->getRecords();
    
  } 
  
  public function getBriefResponse() {
    $response = array();
    $response['hits'] = $this->hits;
    $response['records'] = $this->records;
    return $response;
  }
  
  
}