<?php

namespace Blacklight;
use GuzzleHttp\Client as Client;

/*
 * Blacklight
 * 
 * Basic client to connect to Blacklight
 * 
 * 
 */
 
class Blacklight
{
  protected $http_client;
  protected $host;
  protected $base_url;
  protected $params = array(
    'f1' => 'kw',
  );
  
  function __construct($host, $base, Client $client = null) {
    $this->host = $host;
    $this->base_url = $base;
    if ( $client != null )
    {
      $this->http_client = $client;
    }
    else 
    {
      $this->http_client = new Client(['base_uri' => $this->host]);
    }
    
  }
  
  public function query($string, $index_type) {
    $query = array();
    $query['q'] = $string;
    $query['search_field'] = $index_type;
    $query['format'] = 'json';
    $query['per_page'] = '5';
    $querystring = http_build_query($query);
    $response = $this->send($query);
    return $response;
    
  }
  
  private function send($query) {
    $headers = array(
      "Accept" => "application/json"
      );
    $response = $this->http_client->get($this->base_url, [
      'headers' => $headers,
      'query' => $query,
      'timeout' => 10 ]
      );
    
    return (string)$response->getBody();
  }
  
}
