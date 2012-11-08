<?php

namespace LookupApp\Tests;
use Primo\PermaLink as PermaLink;

/**
 * 
 */
class PermaLinkTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $this->primo_server_connection = array(
      'base_url' => 'http://searchit.princeton.edu',
      'institution' => 'PRN',
      'default_view_id' => 'PRINCETON',
      'default_pnx_source_id' => 'PRN_VOYAGER',
    );
  }
  
  function testReturnValidIDPermaLink() {
    $ids = array("dedupmrg48669359", "PRN_VOYAGER857469", "PRN_VOYAGER5399326");
    foreach($ids as $id) {
      $link = "http://searchit.princeton.edu/primo_library/libweb/action/dlDisplay.do?institution=PRN&vid=PRINCETON&docId={$id}";
      $permalink = new PermaLink($id, $this->primo_server_connection);
      $this->assertEquals($link, $permalink->getLink());
    }
  }
  
  function testGetIDLinkAsSearch() {
    $ids = array("PRN_VOYAGER857469", "PRN_VOYAGER5399326");
    foreach($ids as $id) {
      $link = "http://searchit.princeton.edu/primo_library/libweb/action/dlSearch.do?institution=PRN&onCampus=false&indx=1&dym=true&highlight=true&displayField=title&query=any%2Ccontains%2C{$id}&bulkSize=10&loc=local,scope:(OTHERS),scope:(FIRE)&vid=PRINCETON&tab=location";
      $permalink = new PermaLink($id, $this->primo_server_connection);
      $this->assertEquals($link, $permalink->getDeepLinkAsSearch());
    }
  }
}
