<?php
namespace PrimoServices;

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
  private $base_url = "http://searchit.princeton.edu";
  private $primo_single_record_path = "/primo_library/libweb/action/dlDisplay.do";
  private $institution = "PRN";
  private $vid = "PRINCETON";
  
  
  public function __construct($pnx_id) {
    $this->pnx_id = $pnx_id;
    $this->pnx_link = $this->buildIDLink();
  }
  
  public function getLink() {
    return $this->pnx_link;
  }
  
  private function buildIDLink() {
    return $this->base_url . $this->primo_single_record_path . "?" . "institution=" . $this->institution . "&vid=" . $this->vid . "&docId=" . $this->pnx_id;
  }
}