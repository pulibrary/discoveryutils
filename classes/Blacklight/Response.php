<?php
namespace Blacklight;

/*
 * Response
 *
 * Model a Response from a Query to the Blacklight 7 API
 */

class Response
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
      $parsed_record["title"] = $record["attributes"]["title_display"]["attributes"]["value"];
      if (isset($record["attributes"]["marc_relator_display"])) {
        $parsed_record["relator"] = $record["attributes"]["marc_relator_display"]["attributes"]["value"];
      }
      if (isset($record["attributes"]["author_display"])) {
        $parsed_record["author"] = $record["attributes"]["author_display"]["attributes"]["value"];
      }
      if (isset($record["attributes"]["pub_created_display"])) {
       $parsed_record["publisher"] = $record["attributes"]["pub_created_display"]["attributes"]["value"];
      }
      if (isset($record["attributes"]["holdings_1display"]) ){
        $parsed_record["holdings"] = $record["attributes"]["holdings_1display"]["attributes"]["value"];
      }
      if(isset($record["attributes"]["electronic_portfolio_s"])) {
        $parsed_record["online"] = self::parsePortfolios($record["attributes"]["electronic_portfolio_s"]["attributes"]["value"][0]);
      } elseif(isset($record["attributes"]["electronic_access_1display"])) {
        $parsed_record["online"] = $record["attributes"]["electronic_access_1display"]["attributes"]["value"];
      } else {
        // no online links present
      }
      $parsed_record["id"] = $record["id"];
      $parsed_record["type"] = $record["type"];
      $parsed_record["url"] = $base_url .  $record["id"];
      array_push($records, $parsed_record);
    }

    return $records;
  }

  private function parsePortfolios($data) {
    $data = json_decode($data, true);
    $online = array();
    $label = array();
    array_push($label, $data['title']);
    $online[$data['url']] = $label;
    return $online;
  }
   
}
