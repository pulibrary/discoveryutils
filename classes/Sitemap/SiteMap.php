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
    
    private $urls = array();
    
    function __construct($xml) {
        $sitemap = XmlParser::convertToDOMDocument($xml);
        $this->loadUrls($sitemap);
        
    }
    
   
    
    
    private function loadUrls($sitemap) {
        //var_dump($sitemap);
        $entries = $sitemap->getElementsByTagName("url");
        foreach($entries as $entry) {
            
            $url = new \Sitemap\Url($entry);
            array_push($this->urls, $url->getUrlData());
        }
    
    }
    
    public function getNumUrls() {
        return count($this->urls);
    }
    
    public function getAllUrls() {
        $urls = array();
        foreach($this->urls as $entry) {
            array_push($urls, $entry['loc']);
        }
        return $urls;
    }
    
    public function getDailyUrls() {
        
    }
    
    public function getHourlyUrls() {
        
    }
    
    public function getWeeklyUrls() {
        
    }
    
    public function getNoFrequencyUrls() {
        
    }
    
    public function printUrls() {
        foreach($this->urls as $entry) {
            echo $entry['loc'] . "\n";
        }
    }
}

