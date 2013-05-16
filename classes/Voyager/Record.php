<?php
/**
 * Created by JetBrains PhpStorm.
 * User: KevinReiss
 * Date: 5/16/13
 * Time: 9:25 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Voyager;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Record
{
    function __construct($data, $format="html") {
        if ($format == "html") {
            $this->crawler = new DomCrawler($data);
        }
    }

    public function hasCurrentSerials() {
        if($this->crawler->filter('TR TH:contains("Location has (current):")')->count() > 1) {
            return true;
        } else {
            return false;
        }
    }

    public function isOnOrder() {

        if($this->crawler->filter('TR TH:contains("Order information:")')->count() == 1) {
            return true;
        } else {
            return false;
        }

    }

    public function getOnOrderMessage() {
        $order_messages = array();
        if($this->isOnOrder()) {
            $order_messages = $this->crawler->filter('TR TH:contains("Order information:")')->siblings()->each(function ($node, $i) {
                    return trim($node->nodeValue);
                }

            );
        }

        return $order_messages;

    }

    public function getCurrentSerialHoldings() {
        $serial_holdings = array();
        if($this->hasCurrentSerials()) {
            #$current_listings = $this->crawler->filter('TH:contains("Location has (current):")');
            #print_r($current_listings->count());
            #$serial_holdings['values'] = $current_listings->each(function ($node, $i) {
            #        return $node;
            #
            #    }
            #);
            #$holdings = $this->crawler->filterXPath('//TR/TH[contains("Location has (current):")/following-sibling::TD');
        }
        $num_holdings = $this->crawler->filter('TR TH:contains("Location has (current):")')->count();
        #print_r($this->crawler->filter('TR TH:contains("Location has (current):")')->eq(0)->text());
        $serial_holdings['number'] = $num_holdings;
        return $serial_holdings;

    }
}