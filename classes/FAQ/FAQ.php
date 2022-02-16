<?php

namespace FAQ;
use GuzzleHttp\Client as Client;

/*
 * Pulfa
 *
 * Class to manage transactions with LibAnswers FAQ API
 *
 */

class FAQ
{
  protected $http_client;
  protected $host;
  protected $base_url;
  protected $response_size;
  protected $starting_point;
  protected $params = array(
    'iid' => '344',
    'group_id' => '0',
    'topics' => '0',
    'sort' => 'score',
    'sort_dir' => 'desc',
    'limit' => '20',
    'page' => '1',
    'callback' => '',
  );

  protected $queries = array();

  function __construct($faq_host, $faq_base, Client $client = null) {
    $this->host = $faq_host;
    $this->base_url = $faq_base;
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

    $search_terms = $string;

    $url = $this->host . $this->base_url . "/" . $search_terms;

    $response = $this->http_client->get($url, [
        'query' => $query,
        'timeout' => 5 ]
    );

    // decode the response into array - have to cast to string
    if(http_response_code($response->getStatusCode()) ) {
      return json_decode((string)$response->getBody()->getContents(), true);
    } else {
      return '';
    }

  }

  public function setSize($size) {
    $this->response_size = $size;
  }

  public function setStart($start) {
    $this->starting_point = $start;
  }

}
