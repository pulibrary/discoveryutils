<?php
namespace Arts;

/*
 * Response
 * 
 * Model a Response from a Query to the Black API 
 */

class Response
{

  public static function getResponse($json_data, $host) {
    $response = array();
    $arts_data = json_decode($json_data, true);
    $response["number"] = $arts_data["hits"]["total"];
    $response["records"] = self::getRecords($arts_data["hits"]["hits"], $host);
    return $response;
  }

  public static function getRecords(array $record_list, string $host) {
    $base_url = $host . "/collections/objects/";
    $records = array();
    foreach($record_list as $record) {
      $parsed_record = array();
      $parsed_record["id"] = $record["_id"];
      $parsed_record["url"] = $base_url .  $record["_id"];
      if ($record["_type"] != "makers"){
        $parsed_record["title"] = $record["_source"]["displaytitle"];
        $parsed_record["date"] = $record["_source"]["medium"];
        $parsed_record["creator"]= $record["_source"]["displaymaker"];
        $parsed_record["object_number"] = $record["_source"]["objectnumber"];
      }
      array_push($records, $parsed_record);
    }

    return $records;
  }
}
