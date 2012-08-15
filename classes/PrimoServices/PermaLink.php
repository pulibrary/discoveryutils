<?php
namespace PrimoServices;

use PrimoServices\SearchDeepLink;

/*
 * @Permalink
 * Uses Primo "deep" linking services to return a bookmarkable URL
 * sample http://searchit.princeton.edu/primo_library/libweb/action/dlDisplay.do?institution=PRN&vid=PRINCETON&docId={primo_id}
 * 
 */

class PermaLink 
{
  private $pnx_id;
  private $pnx_link;
  private $base_url;
  private $primo_single_record_path = "/primo_library/libweb/action/dlDisplay.do";
  private $institution ;
  private $vid;
  
  
  public function __construct($pnx_id, $primo_server_connection) {
    $this->primo_server_connection = $primo_server_connection;
    $this->base_url = $this->primo_server_connection['base_url'];
    $this->institution = $this->primo_server_connection['institution'];
    $this->vid = $this->primo_server_connection['default_view_id'];
    $this->pnx_id = $pnx_id;
    $this->pnx_link = $this->buildIDLink();
  }
  
  public function getLink() {
    return $this->pnx_link;
  }
  
  private function buildIDLink() {
    return $this->base_url . $this->primo_single_record_path . "?" . "institution=" . $this->institution . "&vid=" . $this->vid . "&docId=" . $this->pnx_id;
  }
  
  private function buildSearchDeepLink() {
    $deep_search = new SearchDeepLink($this->pnx_id, "any", "contains", $this->primo_server_connection);
    //print_r($this->primo_server_connection);
    //echo $deep_search->getLink();
    return $deep_search->getLink();
  }
  
  public function getDeepLinkAsSearch() {
    return $this->buildSearchDeepLink();
  } 
}