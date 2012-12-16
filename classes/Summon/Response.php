<?php

namespace Summon;
use Summon\Parser;

/*
 * Response
 * 
 * Response from a Query to the Summon API 
 */

class Response
{
  
  protected $records = array(); // Records attached to current response
  protected $db_recommendations = array(); //recommended databases
  public $hits;
  public $queryString;
  public $deep_search_link;
  
  function __construct($summon_api_response = array() ) {
    $this->hits = $summon_api_response['recordCount'];
    $this->queryString = $summon_api_response['query']['queryString'];
    $this->records = Parser::convertToSummonRecords($summon_api_response);
    $this->db_recommendations = Parser::getDatabaseRecommendations($summon_api_response);
    $this->buildDeepSearchLink();
  }  


  public function getBriefResults() {
    $brief_result_set = array();
          //print_r($this->records);
    foreach($this->records as $record) {
      $brief_result = array(
        'url' => $record->link,
        'title' => trim($record->Title[0]),
        'holdings' => $record->hasFullText,
        'format' => $record->ContentType[0],
        'abstract' => $record->Abstract,
        'fulltextavail' => $record->hasFullText,
      );
      array_push($brief_result_set, $brief_result);
    }
    
    return $brief_result_set;
  }
  
  public function getRecommendations() {
    if(count($this->db_recommendations) > 0) {
      return $this->db_recommendations;
    } else {
      return array();
    }
  }
  
  public function buildDeepSearchLink() {
    $this->deep_search_link = $this->queryString;
  }
  
}
