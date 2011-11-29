<?php
namespace PrimoServices;

class PrimoClient
{
  private $xservice_base = "http://searchit.princeton.edu/PrimoWebServices/xservice/getit";
  private $institution = "PRN";
  
  private $primo_base_url;
  private $primo_institution;
  
  function __construct() {
    
  }
  
  public function getID($pnx_id) {
    $xml = file_get_contents($this->xservice_base ."?institution=" . $this->institution ."&docId=".$pnx_id);
    
    return $xml;
  }
  
  /*
   * xservice URL like http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false&query=any,exact,lok&indx=1&bulkSize=50&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=50
   * 
   * send item a primo query object  
   */
  public function doSearch(\PrimoServices\PrimoQuery $query) {
    
  }
  
}