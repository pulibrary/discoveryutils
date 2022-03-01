<?php

namespace Blacklight;
use GuzzleHttp\Client as Client;

/*
 * PulfalightResponse
 *
 * Basic client to connect to Blacklight
 *
 *
 */

class PulfalightResponse
{

    public static function getResponse($json_data, $host) {
      $response = array();
      $blacklight_data = json_decode($json_data, true);
      if (isset($blacklight_data["meta"]["pages"]["total_count"])) {
        $response["number"] = $blacklight_data["meta"]["pages"]["total_count"];
      }
      if (isset($blacklight_data["data"])) {
        $response["records"] = self::getRecords($blacklight_data["data"], $host);
      }
      return $response;
    }

    public static function getRecords(array $record_list, string $host) {
      $base_url = $host . "/catalog/";
      $records = array();
      foreach($record_list as $record) {
        $parsed_record = array();
        // $parsed_record["title"] = $record["readonly_title_tesim"];
        if (isset($record["attributes"]["scopecontent_ssm"]["attributes"]["value"])) {
          $parsed_record["description"] = $record["attributes"]["scopecontent_ssm"]["attributes"]["value"];
        }
        if (isset($record["attributes"]["normalized_date_ssm"]["attributes"]["value"])) {
          $parsed_record["dates"] = $record["attributes"]["normalized_date_ssm"]["attributes"]["value"];
        }
        if (isset($record["attributes"]["repository_ssm"]["attributes"]["value"])) {
         $parsed_record["repository"] = $record["attributes"]["repository_ssm"]["attributes"]["value"];
        }
        if (isset($record["attributes"]["collection_ssm"]["attributes"]["value"])) {
         $parsed_record["collection"] = $record["attributes"]["collection_ssm"]["attributes"]["value"];
        }
        $parsed_record["id"] = $record["id"];
        if (isset($record["type"])) {
          $parsed_record["type"] = $record["type"];
        }
        $parsed_record["url"] = $record["links"]["self"];
        array_push($records, $parsed_record);
      }

      return $records;
    }
}
