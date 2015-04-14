<?php

/*
 *  Hours.php - Client for Library Hours Data
 * 
 */

namespace Hours;
use GuzzleHttp\Client as Client;
use Hours\Location as Location;

class Hours
{
  protected $http_client;
  protected $host;
  protected $locations_base;
  protected $location_data;
  public $locations = array();
  
  function __construct($host, $base, Client $client = null) {
    $this->host = $host;
    $this->locations_base = $base;
    if ( $client != null )
    {
      $this->http_client = $client;
    }
    else 
    {
      $this->http_client = new Client(['base_url' => $this->host]);
    }
    $this->setLocations();
    
  }

  public function getCurrentHoursByLocation() {
    if (count($this->locations) > 0) {
      return $this->locations;
    } else {
      return array('message' => 'No Locations Found');
    }
    //} else {
    //  return array();
    //}

  }

  private function setLocations() {
    $response = $this->http_client->get($this->locations_base);
    $location_data = $response->json();
    foreach($location_data as $location) {
      $library_location = new Location($location);
      if(!empty($library_location->calendar)) {
        $this->locations[] = $library_location;
      }
    }
  }

  public function getCurrentMonth() {
    return date("Y-m");
  }

  public function getCurrentWeek() {
    $cur_year = date("Y");
    $cur_week = date("W");
    return $cur_year . "-W" . $cur_week;
  }

}