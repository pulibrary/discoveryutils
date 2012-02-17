<?php
namespace PrimoServices;

/*
 * @Searchlink
 * Uses Primo "deep" linking services to return a bookmarkable URL for a primo basic search
 * 
 * sample http://searchit.princeton.edu/primo_library/libweb/action/dlSearch.do?institution=PRN&vid=PRINCETON&onCampus=false&indx=1&bulkSize=150&vl(freeText0)=dogs&vl(89332482UI0)=any&query=any,contains,dogs
 * 
 * http://searchit.princeton.edu/primo_library/libweb/action/dlSearch.do?institution=PRN&vid=PRINCETON&onCampus=false&indx=1&bulkSize=150&vl(freeText0)=dogs&vl(89332482UI0)=any&query=any,contains,dogs
 * 
 * http://searchit.princeton.edu/primo_library/libweb/action/dlSearch.do?institution=PRN&vid=PRINCETON&onCampus=false&indx=1&bulkSize=150
 * &vl(freeText0)=9781416987031&vl(89332482UI0)=any&query=any,contains,9781416987031
 * 
 * // &vl(freeText0)=9781416987031 is for form value population 
 * onCampus must be present or an error comes 
 * 
 * scopes do not seem to work 
 * 
 * simple limiters (title, isbin, ?)
 */

class SearchDeepLink
{
  
  private $primo_query;
  private $deep_search_link;
  private $base_url = "http://searchit.princeton.edu"; //FIXME this needs to go to a config value somewhere
  private $primo_deep_search_path = "/primo_library/libweb/action/dlSearch.do?"; //FIXME This should too
  private $vid = "PRINCETON"; // should this be a parameter 
  private $tabs = array("location","summon", "course", "blended");
  private $active_tab;
  
  public function __construct($query, $index_type, $precision_operator, $tab = "location") {
    $this->query = new \PrimoServices\PrimoQuery($query, $index_type, $precision_operator);
    if ($this->isValidTab($tab)) {
      $this->active_tab = $tab;
    }
    $this->buildDeepSearchLink();
  }
  
  private function buildDeepSearchLink() {
    $this->deep_search_link = $this->base_url . $this->primo_deep_search_path . $this->query->getQueryString() . "&vid=" . $this->vid . "&tab=" . $this->active_tab;
  }
  
  public function getLink() {
    return $this->deep_search_link; 
  }
  
  public function isValidTab($tab) {
    if(in_array($tab, $this->tabs)) {
      return TRUE;
    } else {
      return FALSE;
    }
  } 
}
