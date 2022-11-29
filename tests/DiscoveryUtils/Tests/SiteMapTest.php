<?php

namespace DiscoveryUtils\Tests;

/**
 * 
 */
class SiteMapTest extends \PHPUnit\Framework\TestCase {
  
  protected function setUp(): void {
    $sitemap_data = file_get_contents(dirname(__FILE__).'../../../support/sitemap.xml');
    
    $this->sitemap = new \Sitemap\SiteMap($sitemap_data);

  }
  
  function testGetAllUrls() {
      //$this->sitemap->printUrls();
      $this->assertIsArray($this->sitemap->getAllUrls());
      $this->assertEquals(2503, $this->sitemap->getNumUrls());
      
  }
  
  
}

