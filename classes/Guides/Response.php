<?php

namespace Guides;
use \Guides\Parser as GuidesParser,
    \Guides\Link as Link;

class Response 
{
  protected $records = array();
  public $hits;
  public $qString;
  public $more_link;
  public $query;

  function __construct($guides_api_response, $query) {
    $this->records = Parser::convertToGuidesRecords($guides_api_response);
    $this->query = $query;
    $this->more_link = Link::getLink($query);
  }
  
  public function getBriefResponse() {
    $brief_result_set = array();

    foreach($this->records as $record) {
      $brief_result = array(
        'name' => $record->name,
        'url' => $record->url,
        'description' => $record->description,
      );
      array_push($brief_result_set, $brief_result);
    }  
    
    return $brief_result_set;
  }

  public function getRecords() {
    return $this->records;
  }
  
}
