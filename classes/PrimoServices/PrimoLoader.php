<?php
namespace PrimoServices;

class PrimoLoader 
{
  
  public static function loadPNX($xml) {
    return new PrimoRecord($xml);
  }
  
}