<?php

namespace Pudl;
use Guzzle\Service\Client as Client;

/*
 * Pudl
 * 
 * Basic client to connect to Princeton PUDL
 * 
 * 
 */
 
class Pudl
{
  protected $http_client;
  protected $host;
  protected $base_url;
  protected $params = array(
  //v1=woodrow+wilson&     //&v2=&
    'f1' => 'kw',
  );
  
  function __construct($pudl_host, $pudl_base, Client $client = null) {
    $this->host = $pudl_host;
    $this->base_url = $pudl_base;
    if ( $client != null )
    {
      $this->http_client = $client;
    }
    else 
    {
      $this->http_client = new \Guzzle\Http\Client($this->host . $this->base_url);
    }
  }
  
  public function query($string) {
    $query = array();
    $query['v1'] = $string;
    $querystring = http_build_query($query);
    return $this->send($querystring);
  }
  
  private function send($querystring) {
    $response = $this->http_client->get("?" . $querystring)->send();
    
    return (string)$response->getBody();
     
  }
  
}
