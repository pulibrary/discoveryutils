<?php

namespace PrimoServices;

Class PrimoQuery
{
  
   /*
   * xservice URL like http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false&query=any,exact,lok&indx=1&bulkSize=50&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=50
   * 
   * send item a primo query object 
   * should I have a primo results objects 
   */
  
  private $query_string;
  private $query_value; //the actual text of the query "cat"/
  private $index_field;
  private $precision_operator;
  private $query_params = array(
    "institution" => "PRN",
    "onCampus" => "false",
    "indx" => "1",
    "bulkSize" => "10",
    "dym" => "true",
    "highlight" => "true",
    "lang" => "eng",
    "firsthit" => "1",
    "lasthit" => "10"
  );
  
  function __construct($query_value, $index_field = "any", $precision_operator = "exact") {
    $this->query_value = $query_value;
    $this->index_field = $index_field;
    $this->precision_operator = $precision_operator;
    $this->query_params['query'] = $this->buildQuery();
    $this->query_string = $this->buildQueryString($this->query_params);
    
  }
  
  public function getQueryString() {
    return $this->query_string;
  }
  
  private function buildQuery() {
    return $this->index_field . "," . $this->precision_operator . "," . $this->query_value;
  }
  
  private function buildQueryString($query_params) {
     return http_build_query($query_params);
  }

  public function __toString() {
    return $this->query_string;
  }
}
  