<?php

namespace Guides;
use GuzzleHttp\Client as Client;

/*
 * Guides
 * 
 * Class to manage transactions with Princeton Finding Aids Site
 * 
 */
 
class Guides
{
  protected $http_client;
  protected $host;
  protected $base_url;
  protected $response_size;
  protected $starting_point;
  protected $params = array(
  //search_terms=biology
    'site_id' => '77',
    'key' => '79eb11fd3c26374e9785bb06bc3f3961',
    'status' => '1',
  );
  
  protected $queries = array();
  
  function __construct($guides_host, $guides_base, Client $client = null) {
    $this->host = $guides_host;
    $this->base_url = $guides_base;
    if ( $client != null )
    {
      $this->http_client = $client;
    }
    else 
    {
      $this->http_client = new Client(['base_url' => $this->host]);
    }
  }
  
  public function query($string, $start, $qString) {
    $query = array_merge($this->params, $qString);
    $query['search_terms'] = $string;

    $url = $this->base_url;
    $response = $this->http_client->get($url, [
        'query' => $query,
        'timeout' => 5 ]
      );
    return $response->json();
  }
  
  public function setSize($size) {
    $this->response_size = $size;
  } 
  
  public function setStart($start) {
    $this->starting_point = $start;
  }
  
}
