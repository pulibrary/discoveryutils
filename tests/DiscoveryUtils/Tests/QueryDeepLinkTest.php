<?php

namespace DiscoveryUtils\Tests;

/**
 * Tests "Deep Links"
 */

 
class QueryDeepLinkTest extends \PHPUnit_Framework_TestCase {
  
  function setUp() {
      
    $this->primo_server_connection = array(
      'base_url' => 'http://searchit.princeton.edu',
      'institution' => 'PRN',
      'default_view_id' => 'PRINCETON',
      'default_pnx_source_id' => 'PRN_VOYAGER',
    );
  }
  
  function testReturnValidQueryDeepLink() {
  
    $query_strings = array("dogs", "cats and (fish meal)", "9781416987031");
    foreach($query_strings as $query) {
      $tab = "location";
      $link = "http://searchit.princeton.edu/primo_library/libweb/action/dlSearch.do?institution=PRN&onCampus=false&indx=1&dym=true&highlight=true&displayField=title&query=any%2Ccontains%2C" . urlencode($query) . "&bulkSize=10&loc=local,scope:(OTHERS),scope:(FIRE)&vid=PRINCETON&tab={$tab}";
      $searchlink = new \Primo\SearchDeepLink($query, 'any', 'contains', $this->primo_server_connection, $tab, $scopes = array("OTHERS","FIRE"));
      //$this->assertEquals(strlen($link), strlen($searchlink->getLink()));
      $this->assertEquals($searchlink->getLink(), $link);
    }
  }
  
  function testReturnValidQueryDeepLinkWithFacets() {
    $query_strings = array("Journal of Politics", "Nature");
    $facet_to_test = "journals";
    $tab = "location";
    $facet_filter = "facet_rtype,exact,".$facet_to_test;
    foreach ($query_strings as $query) {
       $searchlink = new \Primo\SearchDeepLink($query, 'any', 'contains', $this->primo_server_connection, $tab, $scopes = array("OTHERS","FIRE"), array($facet_filter));
       $this->assertContains(urlencode($facet_filter), $searchlink->getLink());
       $this->assertContains(urlencode($query), $searchlink->getLink());
    }
  }
  
}
