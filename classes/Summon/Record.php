<?php

namespace Summon;

/*
 * \Summon\Record
 * 
 * Represents and Individual Response from Summon
 * 
 * 
 * // use magic isset and get to expose array properties
 * 
 */
 
Class Record 
{
    
  private $record_fields = array();
  
  function __construct($summon_record = array() ) {
    $this->processFields($summon_record);
  }
  
  private function processFields($summon_record) {
    foreach($summon_record as $field => $value) {
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
