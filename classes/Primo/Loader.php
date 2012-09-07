<?php
namespace Primo;
use Primo\Record;

class Loader 
{
  
  public static function loadPNX($xml) {
    return new \Primo\Record($xml);
  }
  
}