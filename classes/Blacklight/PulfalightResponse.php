<?php

namespace Blacklight;
use GuzzleHttp\Client as Client;

/*
 * Pudl
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
      $response["number"] = $blacklight_data["response"]["pages"]["total_count"];
      $response["records"] = self::getRecords($blacklight_data["response"]["docs"], $host);
      return $response;
    }

    public static function getRecords(array $record_list, string $host) {
      $base_url = $host . "/catalog/";
      $records = array();
      foreach($record_list as $record) {
        $parsed_record = array();
        $parsed_record["title"] = $record["readonly_title_tesim"];
        if (isset($record["readonly_creator_tesim"])) {
          $parsed_record["contributor"] = $record["readonly_creator_tesim"];
        }
        if (isset($record["readonly_publisher_tesim"])) {
         $parsed_record["origin"] = $record["readonly_publisher_tesim"];
        }
        if (isset($record["readonly_collections_tesim"])) {
         $parsed_record["collection"] = $record["readonly_collections_tesim"];
        }
        $parsed_record["id"] = $record["id"];
        $parsed_record["type"] = $record["readonly_format_tesim"];
        $parsed_record["url"] = $base_url .  $record["id"];
        array_push($records, $parsed_record);
      }

      return $records;
    }
}
