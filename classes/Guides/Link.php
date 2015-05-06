<?php

namespace Guides;

class Link
{
  
  private $guides_base = "http://libguides.princeton.edu/srch.php?";
  
  function __construct() {

  }
  
  public function getLink($qString, $query) {
    $qString['q'] = $query;

    return $this->guides_base . urldecode(http_build_query($qString)); 
  }
  
}
