<?php

namespace Pudl;
use Pudl\Link as PudlLink;
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
    $this->pudl_id =  $record->getAttribute('id');
    $this->crawler = new DomCrawler($record);
    $this->loadRecord();
  }

  private function loadRecord() {
       $this->pudl_title = $this->crawler->filter('Object title')->text();
       $this->pudl_type = $this->crawler->filter('Object type')->text();
       $this->pudl_collection = $this->crawler->filter('Object collection')->text();
       $this->pudl_origin = $this->crawler->filter('Object origin')->text();
      // $this->pudl_contributor = $this->crawler->filter('Object contributor')->text();
  }
  
  public function getRecordData() {
    return array(
      "title" => $this->pudl_title,
      "id" => $this->pudl_id,
      "type" => $this->pudl_type,
      "collection" => $this->pudl_collection,
      "origin" => $this->pudl_origin,
      "url" => PudlLink::getLink($this->pudl_id),
     // "contributor" => $this->pudl_contributor
    );
  }
  

}
