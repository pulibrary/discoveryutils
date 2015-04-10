<?php

namespace Pulfa;
use GuzzleHttp\Client as Client;

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
      $this->http_client = new Client(['base_url' => $this->host]);
    }
  }
  
  public function query($string, $start, $record_number) {
    $query = array();
    $query['v1'] = $string;
    $query['rpp'] = $record_number;
    $query['start'] = $start;
    $response = $this->http_client->get($this->base_url, ['query' => $query]);
    return $response->xml();
  }
  
  public function setSize($size) {
    $this->response_size = $size;
  } 
  
  public function setStart($start) {
    $this->starting_point = $start;
  }
  
}
