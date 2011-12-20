<?php
namespace PrimoServices;

class PrimoResponse
{
  
  private $type;
  private $hits;
  private $lasthit;
  private $firsthit;
  private $bulk_size; // this should have a defult 
  
  function __construct() {
    $this->hits = $this->setHits();
  }
  
  public function hits() {
    return $this->hits;
    
  }
  
  private function setHits() {
    
  }
  
  
}