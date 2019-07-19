<?php
namespace Blacklight;

/*
 * Response
 * 
 * Model a Response from a Query to the Black API 
 */

class PulmapResponse
{

  public static function getResponse($json_data, $host) {
    $response = array();
    $blacklight_data = json_decode($json_data, true);
    $response["number"] = $blacklight_data["meta"]["pages"]["total_count"];
    $response["records"] = self::getRecords($blacklight_data["data"], $host);
    return $response;
  }

  public static function getRecords(array $record_list, string $host) {
    $base_url = $host . "/catalog/";
    $records = array();
    foreach($record_list as $record) {
      $parsed_record = array();
      $parsed_record["title"] = $record["attributes"]["dc_title_s"]["attributes"]["value"];
      if (isset($record["attributes"]["dc_creator_sm"])) {
        $parsed_record["author"] = $record["attributes"]["dc_creator_sm"]["attributes"]["value"];
      }
      if (isset($record["dc_publisher_s"])) {
        $parsed_record["publisher"] = $record["dc_publisher_s"]["attributes"]["value"];
      }
      $parsed_record["id"] = $record["id"];
      if (isset($record["dc_format_s"])) {
         $parsed_record["type"] = $record["dc_format_s"]["attributes"]["value"];
      }
      $parsed_record["url"] = $base_url .  $record["id"];
      array_push($records, $parsed_record);
    }

    return $records;
  }
}
