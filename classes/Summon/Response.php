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
  public $search_deep_link;
  
  function __construct($summon_api_response) {
    $this->hits = $summon_api_response['recordCount'];
    $this->records = Parser::convertToSummonRecords($summon_api_response);
    $this->db_recommendations = Parser::getDatabaseRecommendations($api_response);
    $this->buildDeepSearchLink();
  }  


  public function getBriefResults() {
    $brief_results = array();
    foreach($this->records as $record) {
      
    }
  }
  
  public function getRecommendations() {
    if(count($this->db_recommendations) > 0) {
      return $this->db_recommendations;
    } else {
      return array();
    }
  }
  
  public function buildDeepSearchLink();
  
}
