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
      $this->http_client = new \Guzzle\Http\Client($this->host);
    }
    
  }
  
  public function query($string) {
    $query = array();
    $query['v1'] = $string;
    $querystring = http_build_query($query);
    $response = $this->send($querystring);
    return $response;
  }
  
  private function send($querystring) {
    // Could not get Guzzle client to get a return value
    // recieved this error 
    //$response = $this->http_client->get($this->base_url . "?" . $querystring)->send();
    //return file_get_contents($this->host . $this->base_url . "?" . $querystring)->send();
    $request_url = $this->host . $this->base_url . "?" . $querystring;
    $ch = curl_init();
    $headers = array('Accept: application/xml');
    curl_setopt($ch, CURLOPT_URL, $request_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
    $response = curl_exec($ch);
    //return (string)$response->getBody();
    curl_close($ch);

    return $response;
  }
  
}
