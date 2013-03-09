<?php

namespace Pudl;


class Link
{
  
  public static function getLink($id) {
    $link_base = "http://pudl.princeton.edu/objects/";
    return $link_base . $id;  
  }
  
}
