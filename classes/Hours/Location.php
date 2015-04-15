<?php

/*
 * Location  
 *
 * Represents Basic Location information about the library.
 */

namespace Hours;

class Location
{
  // example as ics 
  // http://library.princeton.edu/calendar/2015-05/calendar.ics/3688/calendar.ics
  // http://library.princeton.edu/calendar/2015-05/calendar.ics/3688
  private $datafields = array();

  function __construct($params = array()) {
    $this->buildLocationData($params);
  }

  public function setWeeklyDow($params) {

  }

  public function __get($name) {
    if (array_key_exists($name, $this->datafields)) {
      return $this->datafields[$name];
    }
  }
  
  public function __isset($name) {
    return isset($this->datafields[$name]);
  }

  private function buildLocationData($params) {
    foreach($params as $field => $value) {
      if(is_array($value)) {
        $this->datafields[$field] = $value;
      } else {
        $this->datafields[$field] = strip_tags($value);
      }
    }
  }

  public function setLocHours($hours) {
    foreach ($hours as $dailyhours) {
      $this->datafields['hours'][] = $this->cleanWeeklyDow($dailyhours);
    }
    //print_r($this->datafields['hours']);
  }

  private function cleanWeeklyDow($hours) {
    $loc_hours = array();
    $status = $hours['status'];
    $close = strip_tags($hours["close"][0]);
    $open = strip_tags($hours["open"][0]);
    if ($status == "Open") {
      if (empty($hours["dow"])) {
        $dow = 0;
      } else {
        $dow = strip_tags($hours["dow"][0]);
      }
      if (strip_tags($close)<strip_tags($open)) {
        $loc_hours["day"][$dow] = array (
            "open" => $open,"close" => "2359"
        );
        if ($dow==6) {
          $loc_hours["day"][0] = array (
              "open" => "0000","close" => $close
          );
        } else {
          $loc_hours["day"][$dow+1] = array (
              "open" => "0000","close" => $close
          );
        }
      } else {
        $loc_hours["day"][$dow]["open"] = $open;
        $loc_hours["day"][$dow]["close"] = $close;
      }
    }
    return $loc_hours;
  }



}