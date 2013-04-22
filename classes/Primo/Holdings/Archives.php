<?php
namespace Primo\Holdings;

/*
 * Build Archival Holding Structure
 * 
 * Example IDs
 * 
 * Large - XMLC0101_c0
 * 
 * Short - C0751_c004
 * 
 * Shows HOldings level info
 * 
 * * Access Restrictions
 * * Call Number
 * * Other Location Info...
 * 
 */


class Archives
{
  
  private $holding_fields = array();

  public function __construct($archival_params = array()) {
    foreach($archival_params as $key => $value) {
      $this->holding_fields[$key] = $value;
    }
  }
  
  
  public function __get($name) {
    if (array_key_exists($name, $this->holding_fields)) {
      return $this->holding_fields[$name];
    }
  }
  
   public function __isset($name) {
     
      return isset($this->holding_fields[$name]);
   }
  
  
}