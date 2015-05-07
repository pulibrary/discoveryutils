<?php

namespace Guides;

Class Record
{

  private $record_fields = array();

  function __construct($guides_record = array() ) {
    $this->processFields($guides_record);
  }

  private function processFields($guides_record) {
    foreach($guides_record as $field => $value) {
      $this->record_fields[$field] = $value;
    }
  }


  public function __get($name) {
    if (array_key_exists($name, $this->record_fields)) {
      return $this->record_fields[$name];
    }
  }

  public function __isset($name) {

    return isset($this->record_fields[$name]);
  }

}
