<?php

namespace LookupApp\Tests;

/**
 * Tests "Deep Links"
 */

 
class QueryDeepLinkTest extends \PHPUnit_Framework_TestCase {
  
  function testReturnValidQueryDeepLink() {
    $primo_server_connection = array(
      'base_url' => 'http://searchit.princeton.edu',
      'institution' => 'PRN',
      'default_view_id' => 'PRINCETON',
      'default_pnx_source_id' => 'PRN_VOYAGER',
    );
    $query_strings = array("dogs", "cats and (fish meal)", "9781416987031");
    foreach($query_strings as $query) {
      $tab = "location";
      $link = "http://searchit.princeton.edu/primo_library/libweb/action/dlSearch.do?institution=PRN&onCampus=false&indx=1&bulkSize=10&dym=true&highlight=true&displayField=title&query=any%2Ccontains%2C" . urlencode($query) . "&loc=local,scope:(OTHERS),scope:(FIRE)&vid=PRINCETON&tab={$tab}";
      $searchlink = new \Primo\SearchDeepLink($query, 'any', 'contains', $primo_server_connection, $tab, $scopes = array("OTHERS","FIRE"));
      //$this->assertEquals(strlen($link), strlen($searchlink->getLink()));
      $this->assertEquals($searchlink->getLink(), $link);
    }
  }
  
}
