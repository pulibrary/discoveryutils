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
      if (isset($record["attributes"]["readonly_title_ssim"])) {
        $parsed_record["title"] = $record["attributes"]["readonly_title_ssim"]["attributes"]["value"];
      }
      if (isset($record["attributes"]["readonly_creator_ssim"])) {
        $parsed_record["contributor"] = $record["attributes"]["readonly_creator_ssim"]["attributes"]["value"];
      }
      if (isset($record["attributes"]["readonly_publisher_ssim"])) {
       $parsed_record["origin"] = $record["attributes"]["readonly_publisher_ssim"]["attributes"]["value"];
      }
      if (isset($record["attributes"]["readonly_collections_tesim"])) {
       $parsed_record["collection"] = $record["attributes"]["readonly_collections_tesim"]["attributes"]["value"];
      }
      if (isset($record["attributes"]["readonly_format_ssim"])) {
        $parsed_record["type"] = array($record["attributes"]["readonly_format_ssim"]["attributes"]["value"]);
      } else {
        $parsed_record["type"] = array("Other");
      }
      $parsed_record["id"] = $record["id"];
      $parsed_record["url"] = $base_url .  $record["id"];
      array_push($records, $parsed_record);
    }

    return $records;
  }
}
