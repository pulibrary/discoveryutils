<?php
namespace Blacklight;

/*
 * Response
 * 
 * Model a Response from a Query to the Black API 
 */

class Response
{

  public static function getResponse($json_data) {
    $response = array();
    $blacklight_data = json_decode($json_data, true);
    $response["number"] = $blacklight_data["response"]["pages"]["total_count"];
    $response["records"] = self::getRecords($blacklight_data["response"]["docs"]);
    return $response;
  }

  public static function getRecords($record_list) {
    $base_url = "https://pulsearch.princeton.edu/catalog/";
    $records = array();
    foreach($record_list as $record) {
      $parsed_record = array();
      $parsed_record["title"] = $record["title_display"];
      $parsed_record["id"] = $record["id"];
      $parsed_record["type"] = $record["format"];
      $parsed_record["url"] = $base_url .  $record["id"];
      array_push($records, $parsed_record);
    }

    return $records;
  }
}