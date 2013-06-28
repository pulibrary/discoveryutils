<?php

namespace Sitemap;
use Guzzle\Http\Client as HttpClient;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SitemapCrawler
 *
 * @author KevinReiss
 */
class Fetcher {
    //put your code here
    
    public static function loadSiteMap($host) {
        $request =  $this->http_client->get($this->host . "/sitemap.xml");
        $request->addHeader("Accept", "application/xml");
       
    
        $response = $request->send();
  
        return (string)$response->getBody();
  
  }
}

?>
