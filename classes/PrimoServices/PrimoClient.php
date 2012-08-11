<?php
namespace PrimoServices;
use PrimoServices\PrimoQuery;
use Guzzle\Service\Client as Client;

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
    $this->client = new Client($this->baseurl);
  }
  
  public function getID($pnx_id) {
    $request = $this->client->get($this->xservice_base . $this->xservice_getit . "institution=" . $this->institution ."&docId=".$pnx_id);
    $response = $request->send();
    return $response;
  }
  
  /*
   * xservice URL like http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false&query=any,exact,lok&indx=1&bulkSize=50&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=50
   * 
   * send item a primo query object 
   * should I have a primo results objects 
   */
  public function doSearch(PrimoQuery $query) {
    $request = $this->client->get($this->xservice_base . $this->xservice_brief_search . $query->getQueryString());
    $response = $request->send();
    if(strlen($xml) != 0) { 
      return $response; 
    } else {
      return "<error><code>503</code><message>No Response from Primo Server</message></error>";
    }
  }

  /* not sure if this really even useful? */ 
  public function __toString() {
    return $this->client;;
  }
  
  
  
}
