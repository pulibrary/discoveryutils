<?php

namespace Pudl;
use GuzzleHttp\Client as Client;

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
      $this->http_client = new Client(['base_url' => $this->host]);
    }

  }

  public function query($string) {
    $query = array();
    $query['v1'] = $string;
    //$querystring = http_build_query($query);
    $response = $this->send($query);
    return $response;

  }

  private function send($query) {
    $headers = array(
      "Accept" => "application/xml"
      );
    $response = $this->http_client->get($this->host.$this->base_url, [
      'headers' => $headers,
      'query' => $query,
      'timeout' => 5 ]
      );
    //$request->addHeader("Accept", "application/xml");
    //$request->addHeader("Accept-Charset", "UTF-8");

    //$response = $request->send();
    $body = $response->getBody()->getContents();
    if(strlen($body) == 0) {
      $empty_doc = "<Objects start='0' total='0'><facets/></Objects>";
      return $empty_doc;
    } else {
      return (string)$response->getBody();
    }
  }

}
