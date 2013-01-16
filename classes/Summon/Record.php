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
  
  public function getFormattedDate() {
      $date = array();
     
      if(isset($this->record_fields['Volume'])) {
        $date[] =  "Vol. " . $this->record_fields['Volume'][0] . ","; 
      } 
      if(isset($this->record_fields['Issue'])) {
        $date[] = "No. " . $this->record_fields['Issue'][0] . ",";
      }
      if(isset($this->record_fields['PublicationYear'])) {
        $date[] = $this->record_fields['PublicationYear'][0];
      }
      if(isset($this->record_fields['StartPage'])) {
        $date[] = ", pp." . $this->record_fields['StartPage'][0];
      }
      if(isset($this->record_fields['EndPage'])) {
        // if first and last page are equal only show the first one. 
        if($this->record_fields['StartPage'][0] != $this->record_fields['EndPage'][0]) { 
          $date[] = "-" . $this->record_fields['EndPage'][0];
        }
      }
      return implode("", $date);
  }
  
  
  
  public function getISXN() {
    if(isset($this->record_fields['ISSN'])) {
      return $this->record_fields['ISSN'];  
    } elseif(isset($this->record_fields['ISBN'])) {
      return $this->record_fields['ISBN'];  
    } else {
      return NULL;
    }
  }
  
  public function getFormattedAuthor() {
    if(isset($this->record_fields['Author_xml'])) {
      //print_r($this->record_fields['Author_xml']);
      return $this->record_fields['Author_xml'][0]['fullname'];  
    }
  }
  
}
