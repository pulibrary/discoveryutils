<?php

namespace Pudl;


class Response
{
  private $records;
  private $response_uri;
  private $hits;
  private $query;
  
  function __construct($xml, $query) {
    $this->query = $query;
    
  } 
  
  public function getBriefResponse() {
    $response = array();
    $response['query'] = $this->query;
    return $response;
  }
  
}