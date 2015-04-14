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
      $this->datafields[$field] = $value;
    }
  }



}