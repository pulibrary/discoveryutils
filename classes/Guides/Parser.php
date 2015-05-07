<?php

namespace Guides;

class Parser
{
  public static function convertToGuidesRecords($api_response) {
    $records = array();
    if(count($api_response) > 0) {
      foreach($api_response as $result) {
        array_push($records, new Record($result) );
      }
    }
    return $records;
    
  }
}
