<?php

/* 
 * PrimoPNXLoad
 *
 * Generates Primo PNX Record objects
 *
 */

class PNXLoader 
{
  
  public static function loadPNX($xml) {
    return new PrimoRecord($xml);
  }
  
}

class PrimoClient
{
  const PRIMO_BASE_URL = "http://searchit.princeton.edu/PrimoWebServices/xservice/getit";
  const PRIMO_INSTITUTION = "PRN";
  
  public static function getIDRequest($pnx_id) {
    $xml = file_get_contents(PRIMO_BASE_URL ."?institution=" . PRIMO_INSTITUTION ."&docId=".$pnx_id);
    
    return $xml;
  }
}

class PrimoLoadException Extends Exception 
{

}
?>
