<?php

namespace PrimoServices;

Class PrimoQuery
{
  
  private $query_string;
  
  function __construct($query) {
    $this->query_string = $this->buildQuery($query);
    
  }
  
  public function getQueryString() {
    return;
  }
  
  private function buildQuery($query) {
    return;
  }
}
  