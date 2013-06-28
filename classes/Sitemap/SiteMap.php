<?php

namespace Sitemap;
use Utilities\Parser as XmlParser;
/*
 * PHP Representation of a Sitemp
 *
 * 
 */

class SiteMap
{
    function __construct($xml) {
        $sitemap = XmlParser::convertToDOMDocument($xml);
    }
    
    public function getAllUrls() {
        
    }
    
    public function getDailyUrls() {
        
    }
    
    public function getHourlyUrls() {
        
    }
    
    public function getWeeklyUrls() {
        
    }
}

