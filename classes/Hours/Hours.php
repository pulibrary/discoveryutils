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
  private $http_client;
  private $host;
  private $locations_base;
  private $daily_hours;
  private $location_data;
  private $locations = array();
  
  function __construct($host, $base, $weekly_base, Client $client = null) {
    $this->host = $host;
    $this->locations_base = $base;
    $this->daily_hours = $weekly_base;
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
  }

  public function getDowHours() {
    $dow_hours = $this->setDowHours();
    for ($i = 0; $i < count($this->locations); ++$i) {
      if(!empty($this->locations[$i]->calendar)) {
        $this->locations[$i]->setLocHours($dow_hours[$this->locations[$i]->calendar]);
      }
    }
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

  private function setDowHours() {
     $hours_by_cal_id = array();
     $response = $this->http_client->get($this->daily_hours);
     $weekly_hours = $response->json();
     foreach($weekly_hours as $hours) {
       $hours_by_cal_id[$hours['calendar']][] = $hours;
     }
     return $hours_by_cal_id;
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