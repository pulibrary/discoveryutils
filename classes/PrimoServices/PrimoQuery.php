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
  
  function __construct($query) {
    $this->query_string = $this->buildQuery($query);
    
  }
  
  public function getQueryString() {
    return;
  }
  
  /*
   * Returns a "deep link version of the query"
   */
  public function getDeepQueryLink() {
    return;
  }
  
  private function buildQuery($query) {
    return;
  }
}
  