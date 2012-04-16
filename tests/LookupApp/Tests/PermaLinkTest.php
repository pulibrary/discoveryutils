<?php

namespace LookupApp\Tests;
use PrimoServices\PermaLink;

/**
 * 
 */
class PermaLinkTest extends \PHPUnit_Framework_TestCase {
  
  function testReturnValidIDPermaLink() {
    $ids = array("dedupmrg48669359", "PRN_VOYAGER857469", "PRN_VOYAGER5399326");
    foreach($ids as $id) {
      $link = "http://searchit.princeton.edu/primo_library/libweb/action/dlDisplay.do?institution=PRN&vid=PRINCETON&docId={$id}";
      $permalink = new PermaLink($id);
      $this->assertEquals($link, $permalink->getLink());
    }
  }
  
  function testGetIDLinkAsSearch() {
    $ids = array("PRN_VOYAGER857469", "PRN_VOYAGER5399326");
    foreach($ids as $id) {
      $link = "http://searchit.princeton.edu/primo_library/libweb/action/dlSearch.do?institution=PRN&onCampus=false&indx=1&bulkSize=10&dym=true&highlight=true&displayField=title&query=any%2Ccontains%2C{$id}&loc=local,scope:(OTHERS),scope:(FIRE)&vid=PRINCETON&tab=location";
      $permalink = new PermaLink($id);
      $this->assertEquals($link, $permalink->getDeepLinkAsSearch());
    }
  }
}
