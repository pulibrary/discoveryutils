<?php

namespace FAQ;
/*
 * Utility class to create FAQ Record Objects
 */

class Parser
{
  public static function convertToFaqRecords($api_response) {
    $records = array();
    if(isset($api_response['search']['results'])){
      if(count($api_response['search']['results']) > 0) {
        foreach($api_response['search']['results'] as $result) {
          array_push($records, new Record($result) );
        }
      }
    }
    return $records;
  }


}
