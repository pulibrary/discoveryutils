<?php

namespace LookupApp\Tests;

/**
 * 
 */
class LookupPrimoQueryTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    
  }
  
  public function testIsValidQuery() {
    
  }
  
  public function testHasValidScopes() { //Check Scopes examples loc=local, Engineering Only - scope:(ENG) All Princeton loc=local,scope:(PRN) 
    
  }
  
  public function testContainsValidAdaptor() {// Test Summon Adaptor loc=adaptor,SummonThirdNode
    //http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false&query=any,contains,wright&indx=1&bulkSize=1&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=1&loc=adaptor,SummonThirdNode  
  }
  
  public function testCompoundQuery() {
    // with facets http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false&query=facet_topic,exact,Prairie%20school%20(Architecture)&query=any,exact,frank%20lloyd%20wright&indx=1&bulkSize=1&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=1&loc=local,scope:(ARCH)
  }
  
  public function testCompundQueryWithFacets() {
    
  }
  
  public function testCompoundQueryWithSubjects() {
    
  }
  
  public function testSingleQuery() {
    
  }
  
  public function testIndexFieldTypesQuery() { //Go Through All Available Index Fields
    
  }
  
  public function testQueryPrecisionOperators() {
    
  }
  
  public function testQueryStringHasNoCommas() { //Primo API and Deep Links queries can't have commas 
    
  }
}