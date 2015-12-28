<?php 

/*
 * Daily Hours Scraper
 */

namespace Hours;
use GuzzleHttp\Client as Client;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class Day {

  private $http_client;
  private $host;

  function __construct($host, $daily_base, Client $client = null) {
    $this->host = $host;
    $this->daily_hours = $daily_base;
    if ( $client != null )
    {
      $this->http_client = $client;
    }
    else 
    {
      $this->http_client = new Client(['base_url' => $this->host]);
    }
    
  }

  public function getDailyHoursByLocation() {
    $ids = array('3700', '3709');
    $response = $this->http_client->get($this->daily_hours);
    $hours_body = $response->getBody()->getContents();
    $hours_of_day = $this->scrapeDailyHours($hours_body);
    return $hours_of_day;
  }

  private function scrapeDailyHours($html_body) {
    
  //    * Data attributes 
  //     data-name-{id}
  //     data-status-{id} 
  //     data-hours-{id}
  //    RBSC = 3700 
  //    MUDD = 3709
    $hours_data = array();
    $dom = new Crawler($html_body);
    $mudd_status = $dom->filter('.data-status-3700')->each(function (Crawler $node, $i) {
      return trim($node->text());
    });
    $hours_data["mudd"] = $mudd_status[0];
    $rbsc_status = $dom->filter('.data-status-3709')->each(function (Crawler $node, $i) {
      return trim($node->text());
    });
    $hours_data["rbsc"] = $rbsc_status[0];
    $mudd_hours = $dom->filter('.data-hours-3700')->each(function (Crawler $node, $i) {
      return trim($node->text());
    });
    $hours_data["mudd-hours"] = $mudd_hours[0];
    $rbsc_hours = $dom->filter('.data-hours-3709')->each(function (Crawler $node, $i) {
      return trim($node->text());
    });
    $hours_data["rbsc-hours"] = $rbsc_hours[0];
    //print_r($hours_data);
    return $hours_data;
  }

}