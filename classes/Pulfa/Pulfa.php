<?php

namespace Pulfa;
use Guzzle\Service\Client as Client;
use Pulfa\Query;

/*
 * Pulfa
 * 
 * Class to manage transactions with Princeton Finding Aids Site
 * 
 */
 
class Pulfa
{
  protected $http_client;
  protected $host;
  protected $base_url;
  protected $response_size;
  protected $starting_point;
  protected $params = array(
  //v1=woodrow+wilson&     //&v2=&
    'f1' => 'kw',
    'b1' => 'AND',
    'f2' => 'kw',
    'b2' => 'AND',
     //&v3=&
    'f3' => 'kw',
    'year' => 'before',
    //&ed=&ld=&
    'rpp' => '10',
    'start' => '0',
  );
  
  protected $queries = array();
  
  function __construct($pulfa_host, $pulfa_base, Client $client = null) {
    $this->host = $pulfa_host;
    $this->base_url = $pulfa_base;
    if ( $client != null )
    {
      $this->http_client = $client;
    }
    else 
    {
      $this->http_client = new \Guzzle\Http\Client($this->host . $this->base_url);
    }
  }
  
  public function query($string, $start, $record_number) {
    $query = array();
    $query['v1'] = $string;
    $query['rpp'] = $record_number;
    $query['start'] = $start;
    $querystring = http_build_query($query);
    return $this->send($querystring);
  }
  
  private function send($querystring) {
    $response = $this->http_client->get("?" . $querystring)->send();
    
    return (string)$response->getBody();
     
  }
  
  public function setSize($size) {
    $this->response_size = $size;
  } 
  
  public function setStart($start) {
    $this->starting_point = $start;
  }
  
  public function setQuery() {
    
  }
}
