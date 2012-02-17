<?php
namespace PrimoServices;

class SummonQuery {
  
  private $summon_base = "http://princeton.summon.serialssolutions.com/search?";
  private $query_key = "s.q";
  private $query;
  private $limiters;
  private $url;
  
  
  function __construct($query, $limiters = array()) {
    $this->limiters = $limiters;
    $this->query = $query;
    $this->buildLink();
  }
  
  private function buildLink() {
    $query_string = array("{$this->query_key}={$this->query}");   
    if(count($this->limiters > 0)) {
      foreach($this->limiters as $limiter => $limit_value) {
        array_push($query_string, "{$limiter}={$limit_value}");
      }
    }
    $this->url = $this->summon_base . implode('&', $query_string);
  }
  
  public function getLink() {
    return $this->url;
  }
}


?>