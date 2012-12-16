<?php
namespace Primo;
use Primo\Query;
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
  
  private $query;
  private $deep_search_link;
  private $base_url; 
  private $primo_deep_search_path = "/primo_library/libweb/action/dlSearch.do?"; //FIXME This should too
  private $vid; // should this be a parameter 
  private $tabs = array("location","summon", "course", "blended");
  private $active_tab;
  private $facet_filters = array();
  
  public function __construct($query, $index_type, $precision_operator, $primo_connection, $tab = "location", $scopes = array("OTHERS","FIRE"), $facet_list = array()) {
    $this->query = new \Primo\Query($query, $index_type, $precision_operator, $scopes);
    $this->base_url = $primo_connection['base_url'];
    $this->vid = $primo_connection['default_view_id'];
    if ($this->isValidTab($tab)) {
      $this->active_tab = $tab;
    }
    $this->facet_filters = $facet_list;
    if(count($this->facet_filters > 0)) {
      foreach($this->facet_filters as $filter) {
        $this->query->addFacet($filter);
      }
    }
    $this->buildDeepSearchLink();
  }
  
  private function buildDeepSearchLink() {
    $this->deep_search_link = $this->base_url . $this->primo_deep_search_path . $this->query->getQueryString() . $this->query->buildFacets() . "&vid=" . $this->vid . "&tab=" . $this->active_tab;
    //print_r($this->deep_search_link);
  }
  
  public function getLink() {
    //echo "base url: " . $this->base_url . "\n";
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
