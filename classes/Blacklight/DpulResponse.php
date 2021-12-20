<?php
namespace Blacklight;

/*
 * Response
 *
 * Model a Response from a Query to the Black API
 */

class DpulResponse
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
      // if (isset($record["readonly_title_tesim"])) {
      //   $parsed_record["title"] = $record["readonly_title_tesim"];
      // }
      // if (isset($record["readonly_creator_tesim"])) {
      //   $parsed_record["contributor"] = $record["readonly_creator_tesim"];
      // }
      // if (isset($record["readonly_publisher_tesim"])) {
      //  $parsed_record["origin"] = $record["readonly_publisher_tesim"];
      // }
      // if (isset($record["readonly_collections_tesim"])) {
      //  $parsed_record["collection"] = $record["readonly_collections_tesim"];
      // }
      // if (isset($record["readonly_format_tesim"])) {
      //   $parsed_record["type"] = $record["readonly_format_tesim"];
      // }
      // $parsed_record["id"] = $record["id"];
      // $parsed_record["url"] = $base_url .  $record["id"];
      $parsed_record["id"] = $record["id"];
      $parsed_record["type"] = $record["type"];
      $parsed_record["url"] = $base_url .  $record["id"];
      array_push($records, $parsed_record);
      array_push($records, $parsed_record);
    }

    return $records;
  }
}
