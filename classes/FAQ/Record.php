<?php

namespace FAQ;


/*
 * \FAQ\Record
 *
 * Represents and Individual Response from Libanswers
 *
 *
 * // use magic isset and get to expose array properties
 *
 */


Class Record
{

  private $record_fields = array();

  function __construct($faq_record = array() ) {
    $this->processFields($faq_record);
  }

  private function processFields($faq_record) {
    foreach($faq_record as $field => $value) {
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
