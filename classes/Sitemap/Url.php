<?php

namespace Sitemap;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Description of Url
 *
 * @author KevinReiss
 */
class Url {
    //put your code here
    private $url_data = array();
    
  function __construct(\DOMElement $url) {
   
    if ($url->hasChildNodes()) {
        $this->loadUrl($url);
    }
  }

  private function loadUrl($url) {
       $properties = array("loc", "lastmod", "priority", "changefreq");
       $children = $url->childNodes;
       foreach($children as $child) {
           $this->url_data[$child->tagName] = $child->textContent;          
       }
         
       
  }
  
  public function getUrlData() {
    return $this->url_data;
  }
}

