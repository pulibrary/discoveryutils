<?php

namespace Blacklight;
use GuzzleHttp\Client as Client;


class Record
{

  public static function getTitle($record_url) {
    $client = new Client(['base_url' => $record_url]);
    $headers = array(
      "Accept" => "application/json"
      );
    $response = $client->get($record_url . ".json", [
      'headers' => $headers,
      'timeout' => 5 ]
      );
    $marc_data = json_decode((string)$response->getBody(), true);

    $fields = $marc_data["fields"];
    $title = "";
    foreach ($fields as $field) {
      foreach ($field as $key => $value) {
        if($key == 245) {
          $chars_to_ignore = $value["ind2"];
          if ($chars_to_ignore >= 1) {
            $title = substr($value["subfields"][0]['a'], $chars_to_ignore);
          } else {
            $title = $value["subfields"][0]['a'];
          }
        }
      }
    }
    return $title;
  }
}