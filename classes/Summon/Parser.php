<?php

namespace Summon;
use Summon\Record;

/*
 * Utility class to create Summon Record Objects 
 */
 
class Parser 
{
  public static function convertToSummonRecords($api_response) {
    $records = array();
    if(count($api_response['documents']) > 0) {
      foreach($api_response['documents'] as $document) {
        array_push($records, new Record($document) );
      }
    }
    return $records;
  }
  
  public static function getDatabaseRecommendations($api_response) {
    $recommendations = array();
    if(count($api_response['recommendationLists']) > 0) {
      foreach($api_response['recommendationLists'] as $recommendation) {
        array_push($recommendations, $recommendation);
      }
    }
    
    return $recommendations;
  }
  
}
