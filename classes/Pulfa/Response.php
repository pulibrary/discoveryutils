<?php

namespace Pulfa;
use \Pulfa\Parser as PulfaParser,
    \Pulfa\Link as Link;

class Response 
{
  private $records;
  private $response_uri;
  private $hits;
  
  function __construct($xml, $query) {
    $parsed_response_data = New PulfaParser($xml);
    $this->records = $parsed_response_data->getRecords();
    $this->hits = $parsed_response_data->getHits();
    $this->response_uri = $parsed_response_data->getQueryUri();
    $this->more_link = new Link($query);
  }
  
  
  public function getBriefResponse() {
    $response = array();
    $response['number'] = $this->hits;
    $response['more'] = $this->more_link->getLink();
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
