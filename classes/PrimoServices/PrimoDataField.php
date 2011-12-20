<?php
namespace PrimoServices;

/*
 * @PrimoDataField
 *
 */
 
class PrimoDataField 
{
  private $label;
  private $key;
  private $value;
  
  public function __construct($values = array()) {
    $this->key = $values['key'];
    $this->label = $values['label'];
    $this->value = $values['values']; 
  }
  
}
 