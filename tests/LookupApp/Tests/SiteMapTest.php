<?php

namespace LookupApp\Tests;

/**
 * 
 */
class SiteMapTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $sitemap_data = file_get_contents(dirname(__FILE__).'../../../support/sitemap.xml');
    
    $this->sitemap = new \Sitemap\SiteMap($sitemap_data);

  }
  
  function testGetAllUrls() {
      $this->sitemap->printUrls();
      $this->assertInternalType('array', $this->sitemap->getAllUrls());
      $this->assertEquals(2463, $this->sitemap->getNumUrls());
      
  }
  
  
}

