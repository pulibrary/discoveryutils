<?php
namespace PrimoServices;
use PrimoServices\PrimoQuery;

class PrimoClient
{
  private $base_url;
  private $xservice_base = "/PrimoWebServices";
  private $xservice_brief_search = "/xservice/search/brief?"; //run a primo search
  private $xservice_getit = "/xservice/getit?"; // for straight ID lookups 
  private $institution;
  private $current_url;
  private $primo_base_url;
  private $primo_institution;
  
  function __construct($primo_server_connection) {
    $this->base_url = $primo_server_connection['base_url'];
    $this->institution = $primo_server_connection['institution'];
  }
  
  public function getID($pnx_id) {
    $this->current_url = $this->base_url . $this->xservice_base . $this->xservice_getit . "institution=" . $this->institution ."&docId=".$pnx_id;
    //echo $this->current_url;
    //echo $this->xservice_base;
    $xml = file_get_contents($this->current_url);
    
    return $xml;
  }
  
  /*
   * xservice URL like http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false&query=any,exact,lok&indx=1&bulkSize=50&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=50
   * 
   * send item a primo query object 
   * should I have a primo results objects 
   */
  public function doSearch(PrimoQuery $query) {
    $this->current_url = $this->base_url . $this->xservice_base . $this->xservice_brief_search . $query->getQueryString();
    $xml = file_get_contents($this->current_url);
    if(strlen($xml) != 0) { 
      return $xml; 
    } else {
      return "<error><code>503</code><message>No Response from Primo Server</message></error>";
    }
  }
  
  public function __toString() {
    return $this->current_url;
  }
  
  
  
}