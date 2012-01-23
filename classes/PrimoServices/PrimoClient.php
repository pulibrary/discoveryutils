<?php
namespace PrimoServices;

class PrimoClient
{
  private $xservice_base = "http://searchit.princeton.edu/PrimoWebServices/";
  private $xservice_brief_search = "xservice/search/brief?";
  private $xservice_getit = "xservice/getit?";
  private $institution = "PRN";
  private $current_url;
  private $primo_base_url;
  private $primo_institution;
  
  function __construct() {
    
  }
  
  public function getID($pnx_id) {
    $this->current_url = $this->xservice_base . $this->xservice_getit . "institution=" . $this->institution ."&docId=".$pnx_id;
    $xml = file_get_contents($this->current_url);
    
    return $xml;
  }
  
  /*
   * xservice URL like http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false&query=any,exact,lok&indx=1&bulkSize=50&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=50
   * 
   * send item a primo query object 
   * should I have a primo results objects 
   */
  public function doSearch(\PrimoServices\PrimoQuery $query) {
    $this->current_url = $this->xservice_base . $this->xservice_brief_search . $query->getQueryString();
    $xml = file_get_contents($this->current_url);
    
    return $xml;
  }
  
  public function __toString() {
    return $this->current_url;
  }
  
}