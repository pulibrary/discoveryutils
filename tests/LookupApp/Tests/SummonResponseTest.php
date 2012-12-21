<?php

namespace LookupApp\Tests;

/**
 * Tests "Summon Query API"
 */

 
class SummonResponseTest extends \PHPUnit_Framework_TestCase  
{
  protected function setUp() {
    $this->summon_response_data = json_decode(file_get_contents(dirname(__FILE__).'../../../support/summon_response.json'), TRUE);
    
  }
  
  function testIsValidResponse() {
       
  }
  
  
  function testGetResearchGuidesOnlyPrinceton() {
    
  }
  
  function getOnlyPrincetonHoldings() {
    
  }
  
  function getRecommendations() {
    
  } 
  
}