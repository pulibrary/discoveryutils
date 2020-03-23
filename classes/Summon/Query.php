<?php
namespace Summon;

class Query {
  
  private $summon_base = "https://princeton.summon.serialssolutions.com/search?";
  private $query_key = "s.q";
  private $query;
  private $limiters;
  private $url;
  
  
  function __construct($query, $limiters = array()) {
    $this->limiters = $limiters;
    $this->query = urlencode($query);
    $this->buildLink();
  }
  
  private function buildLink() {
    $query_string = array("{$this->query_key}={$this->query}");   
    if(sizeof($this->limiters) > 0) {
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
