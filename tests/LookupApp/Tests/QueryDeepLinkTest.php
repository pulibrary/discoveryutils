<?php

namespace LookupApp\Tests;

/**
 * Tests "Deep Links"
 */

 
class QueryDeepLinkTest extends \PHPUnit_Framework_TestCase {
  
  function testReturnValidQueryDeepLink() {
    $query_strings = array("dogs", "cats and (fish meal)", "9781416987031");
    foreach($query_strings as $query) {
      $link = "http://searchit.princeton.edu/primo_library/libweb/action/dlSearch.do?". urlencode("institution=PRN&vid=PRINCETON&onCampus=false&indx=1&bulkSize=150".
      "&vl(freeText0)={$query}&vl(89332482UI0)=any&query=any,contains,{$query}");
      http://searchit.princeton.edu/primo_library/libweb/action/dlSearch.do?institution=PRN&onCampus=false&indx=1&bulkSize=10&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=10&query=any%2Ccontains%2Cdogs&vid=PRINCETON
      $searchlink = new \PrimoServices\SearchDeepLink($query, 'any', 'contains');
      $this->assertEquals(strlen($link), strlen($searchlink->getLink()));
      $this->assertEquals($searchlink->getLink(), $link);
    }
  }
  
}
