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
    $response["number"] = $blacklight_data["response"]["pages"]["total_count"];
    $response["records"] = self::getRecords($blacklight_data["response"]["docs"], $host);
    return $response;
  }

  public static function getRecords(array $record_list, string $host) {
    $base_url = $host . "/catalog/";
    $records = array();
    foreach($record_list as $record) {
      $parsed_record = array();
      $parsed_record["title"] = $record["dc_title_s"];
      if (isset($record["author_display"])) {
        $parsed_record["author"] = $record["dc_creator_sm"];
      }
      if (isset($record["pub_created_display"])) {
       $parsed_record["publisher"] = $record["dc_publisher_s"];
      }
      $parsed_record["id"] = $record["layer_slug_s"];
      if (isset($record["dc_format_s"])) {
        $parsed_record["type"] = $record["dc_format_s"];
      }
      $parsed_record["url"] = $base_url .  $record["layer_slug_s"];
      array_push($records, $parsed_record);
    }

    return $records;
  }
}
