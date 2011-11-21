<?php
namespace PrimoServices;

class PrimoClient
{
  private $base = "http://searchit.princeton.edu/PrimoWebServices/xservice/getit";
  private $institution = "PRN";
  
  private $primo_base_url;
  private $primo_institution;
  
  function __construct() {
    
  }
  public function getID($pnx_id) {
    $xml = file_get_contents($this->base ."?institution=" . $this->institution ."&docId=".$pnx_id);
    
    return $xml;
  }
  
}