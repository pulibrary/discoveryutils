<?php

namespace LookupApp\Tests;

/**
 * 
 */
class PermaLinkTest extends \PHPUnit_Framework_TestCase {
  
  function testReturnValidIDPermaLink() {
    $ids = array("dedupmrg48669359", "PRN_VOYAGER857469", "PRN_VOYAGER5399326");
    foreach($ids as $id) {
      $link = "http://searchit.princeton.edu/primo_library/libweb/action/dlDisplay.do?institution=PRN&vid=PRINCETON&docId={$id}";
      $permalink = new \PrimoServices\PermaLink($id);
      $this->assertEquals($link, $permalink->getLink());
    }
  }
}
