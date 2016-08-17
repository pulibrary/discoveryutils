<?php 
namespace Blacklight;

class SearchLink
{

  private $query;
  private $host;
  private $path = '/catalog?utf8=✓&search_field=all_fields&q=';
  private $deep_link;
  private $solr_params;

  function __construct($host, $query, $solr_params = null) {
    $this->query = $query;
    $this->host = $host;
    $this->solr_params = $this->buildSolrParams($solr_params);
    $this->deep_link = $this->buildDeepLink();
  }

  public function getLink() {
    return $this->deep_link;
  }

  private function buildDeepLink() {
    $base_url = $this->host . $this->path . $this->query;
    if(isset($this->solr_params)) {
      $base_url .= $this->solr_params;
    }
    return $base_url;
  }

  private function buildSolrParams($params) {
    $solr_string = "";
    if (array_key_exists('format', $params)) {
      $solr_string .= "&f%5Bformat%5D%5B%5D=" . $params['format'];
    }
    if (array_key_exists('location', $params)) {
      $solr_string .= "&f%5Blocation%5D%5B%5D=" . $params['location'];
    }
    return $solr_string;
  }
}