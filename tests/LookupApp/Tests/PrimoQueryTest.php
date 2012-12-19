<?php

namespace LookupApp\Tests;


/**
 * 
 */
class PrimoQueryTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $this->app['primo'] = array(
      'base_url' => 'http://searchit.princeton.edu',
      'institution' => 'PRN',
      'default_view_id' => 'PRINCETON',
      'default_pnx_source_id' => 'PRN_VOYAGER',
      'default_scope' => array('PRN'),
      'default.search' => "contains",
      'num.records.brief.display' => 3
    );
    
  }
  
  public function testIsValidQuery() {
    $query = new \Primo\Query("cats", "any", $this->app['primo']['default.search'], $this->app['primo']['default_scope'], $this->app['primo']['num.records.brief.display']);
    $this->assertContains("&query=any%2Ccontains%2Ccats" , $query->getQueryString());
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
  
  public function testIndexFieldTypesQuery() { //Go Through All Available Index Fields
    
  }
  
  public function testQueryPrecisionOperators() {
    
  }
  
  public function testQueryValueHasNoCommas() { //Primo API and Deep Links queries can't have commas 
    
  }
  
  public function testHasFacet() {
    $query = new \Primo\Query("journal of politics", "title", "exact", $this->app['primo']['default_scope'], $this->app['primo']['num.records.brief.display']);
    $this->assertFalse($query->hasFacets());
    $query->addFacet("facet_rtype,exact,journals");
    $this->assertTrue($query->hasFacets());
    $this->assertEquals(1, count($query->getFacets()));
    $facets = $query->getFacets();
    $this->assertEquals($facets[0], "facet_rtype,exact,journals");
  }
  
  public function testHasMultipleFacets() {
    $query = new \Primo\Query("journal of politics", "title", "exact", $this->app['primo']['default_scope'], $this->app['primo']['num.records.brief.display']);
    $this->assertFalse($query->hasFacets());
    $query->addFacet("facet_rtype,exact,journals");
    $query->addFacet("facet_topic,exact,united states");
    $this->assertTrue($query->hasFacets());
    $this->assertEquals(2, count($query->getFacets()));
    $facets = $query->getFacets();
    $this->assertEquals($facets[0], "facet_rtype,exact,journals");
    $this->assertEquals($facets[1], "facet_topic,exact,united states");
  }
}