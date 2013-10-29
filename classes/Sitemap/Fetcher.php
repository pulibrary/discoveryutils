<?php

namespace Sitemap;
use Guzzle\Http\Client as HttpClient;


/**
 * Description of SitemapFetcher
 *
 * @author KevinReiss
 */
class Fetcher {
    //put your code here
    
    public static function loadSiteMap($host) {
        $request =  $this->http_client->get($host . "/sitemap.xml");
        $request->addHeader("Accept", "application/xml");
       
    
        $response = $request->send();
  
        return (string)$response->getBody();
  
  }
}

?>
