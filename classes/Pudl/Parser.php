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
    $root_element = $this->crawler->filter('Object')->first()->attr('total');//filter('Objects')->first()->attr('total');
    $this->hits = $root_element;
    $this->records = $this->loadRecords();
  }
 
  public function getHits() {
    //how to get attributes?
    
    return $this->hits;
  }
  
  private function loadRecords() {
    /* closure returns an array of records */
    $records = $this->crawler->filter('Objects Object')->each(function ($node, $i) {
      $record = new PudlRecord($node);  
      return $record->getRecordData();
    });
  
    return $records;
    
  }
  
  public function getRecords() {
    return $this->records;
  }
  
}
