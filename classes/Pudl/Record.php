<?php

namespace Pudl;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;


class Record
{

  private $pudl_title;
  private $pudl_id;
  private $pudl_type;
  private $pudl_collection;
  private $pudl_origin;
  private $pudl_contributor;

  function __construct(\DOMElement $record) {
    $this->crawler = new DomCrawler($record);
    $this->loadRecords();
  }

  private function loadRecords() {
       
  }
  
  public function getRecords() {
    return array(
      "title" => $this->pudl_title,
      "id" => $this->pudl_id,
      "type" => $this->pudl_type,
      "collection" => $this->pudl_collection,
      "origin" => $this->pudl_origin,
      "contributor" => $this->pudl_contributor
    );
  }
  

}
