<?php

namespace Pulfa;

class Link
{

  /* sample http://findingaids.princeton.edu/collections?v1=dixon&start=0&rpp=3 */
  private $pulfa_base = "http://pulfa.princeton.edu/collections?";
  private $default_items = "10";
  private $default_start = "0";

  function __construct($query) {
    $this->query = $query;
  }

  public function getLink() {
    $pulfa_params = array(
      'v1' => $this->query,
      'start' => $this->default_start,
      'rpp' => $this->default_items,
    );

    return $this->pulfa_base . http_build_query($pulfa_params);
  }

}
