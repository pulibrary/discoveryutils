<?php
namespace PrimoServices;
use PrimoServices\PrimoRecord;

class PrimoLoader 
{
  
  public static function loadPNX($xml) {
    return new PrimoRecord($xml);
  }
  
}