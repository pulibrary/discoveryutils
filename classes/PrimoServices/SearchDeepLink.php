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
  private $base_url = "http://searchit.princeton.edu";
  private $primo_single_record_path = "/primo_library/libweb/action/dlSearch.do?";
  private $brief_search_params = array(
    "institution" => "PRN",
    "vid" => "PRINCETON",
    "onCampus" => "false",
    "indx" => "1",
    "bulksize" => "150",  
  );
  
  public function __construct($query) {
    $this->query = $query;
    $this->deep_search_link = $this->build_deep_link();
  }
  
  private function build_deep_link() {
    return $this->base_url . $this->primo_single_record_path . urlencode(implode("&", $this->build_param_string()) . $this->build_query_string());
  }
  
  private function build_query_string() {
    return "&vl(freeText0)={$this->query}&vl(89332482UI0)=any&query=any,contains,{$this->query}";
  }
  
  private function build_param_string() {
    $query_params = array();
    foreach($this->brief_search_params as $param => $value) {
      array_push($query_params, "{$param}={$value}");
    }
    
    return $query_params;
  }
  
  public function getLink() {
    //echo $this->deep_search_link;
    return $this->deep_search_link; 
  }
  
}