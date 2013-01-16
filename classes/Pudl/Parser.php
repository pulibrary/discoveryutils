<?php

namespace Pudl;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Pudl\Record as PudlRecord;



class Parser
{
  private $records = array();
  private $hits;
  
  function __construct($xml) {
    $this->crawler = new DomCrawler($xml);
    //$this->hits = $this->crawler->filter('Objects')->first()->attr('total');
    $this->records = $this->loadRecords();
  }
 
  public function getHits() {
    //how to get attributes?
    
    return $this->hits;
  }
  
  private function loadRecords() {
    /* closure returns an array of records */
    $records = $this->crawler->filter('Object')->each(function ($node, $i) {
      $record = new PudlRecord($node);  
      return $record->getRecords();
    });
  
    return $records;
    
  }
  
  public function getRecords() {
    return $this->records;
  }
  
}
