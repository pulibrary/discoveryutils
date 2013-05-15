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
    $this->records = $this->loadRecords();
    $this->hits = count($this->records);
  }
 
  public function getHits() {
    /*   
    $root_element = $this->crawler->filterXPath("/");//->extract(array('total', 'start', 'next')); //->attr('total');
    foreach ($root_element as $domElement) {
      foreach($domElement->attributes as $value) {
        print_r($value); 
      }
    }
    //var_dump($root_element);
    return $root_element;
     */
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
