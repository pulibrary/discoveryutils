<?php

namespace Arts;

class Link
{
  public static function getLink($base, $query, $qString = array()) {
    $qString['q'] = $query;
    return $base . urldecode(http_build_query($qString)); 
  }
}
