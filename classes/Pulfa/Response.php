<?php

namespace Pulfa;
use \Pulfa\Parser as PulfaParser;

class Response 
{
  private $records;
  private $response_uri;
  private $hits;
  
  function __construct($xml) {
    $parsed_response_data = New PulfaParser($xml);
    $this->records = $parsed_response_data->getRecords();
    $this->hits = $parsed_response_data->getHits();
    $this->response_uri = $parsed_response_data->getQueryUri();
  }
  
  
  public function getBriefResponse() {
    $response = array();
    $response['number'] = $this->hits;
    $response['more'] = $this->response_uri;
    foreach($this->records as $record) {
      $response['records'][] = array(
        'title' => $record->title,
        'url' => $record->uri,
        'digital' => $record->digital,
        'abstract' => $record->hits,
        'type' => $record->type,
        'breadcrumb' => $record->breadcrumb,
      );
    }  
    
    return $response;
  }
  
}
