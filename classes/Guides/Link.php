<?php

namespace Guides;

class Link
{
  
  public static function getLink($query, $qString = array()) {
    $qString['q'] = $query;
    $guides_link_base = "http://libguides.princeton.edu/srch.php?";
    return $guides_link_base . urldecode(http_build_query($qString)); 
  }
  
}
