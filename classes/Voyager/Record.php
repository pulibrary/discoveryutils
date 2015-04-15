<?php
/**
 * User: Kevin Reiss
 * Date: 5/16/13
 * Time: 9:25 AM
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

                    return trim($node->eq(0)->text());
                }

            );
        }

        return $order_messages;

    }

    public function getCurrentSerialHoldings() {
        $serial_holdings = array();
        if($this->hasCurrentSerials()) {
            $current_listings = $this->crawler->filter('TH:contains("Location has (current):")');

            $serial_holdings['values'] = $current_listings->each(function ($node, $i) {
                    #$current_holdings_string = $node->siblings()->eq(0)->text();
                    return $node->siblings()->eq(0)->text();


                }
            );
            #$holdings = $this->crawler->filterXPath('//TR/TH[contains("Location has (current):")/following-sibling::TD');
        }
        $num_holdings = $this->crawler->filter('TR TH:contains("Location has (current):")')->count();
        #print_r($this->crawler->filter('TR TH:contains("Location has (current):")')->eq(0)->text());
        $serial_holdings['number'] = $num_holdings;

        return $serial_holdings;

    }

    public function getLocations() {
        $serial_holdings = array();
        /* FIXME - Can't scrap holding details properly
        if($this->hasCurrentSerials()) {
            $current_listings = $this->crawler->filter('TH:contains("Location:")');

            #$next_current = $current_listings->parents()->siblings()->filter('TR TH:contains("Location has (current):")');
            #echo $next_current->siblings()->eq(0)->text();
            #$current_listings->siblings()->each(function ($node, $i) {
            #    echo $node->eq(0)->text();
            #});
            $serial_holdings['values'] = $current_listings->each(function ($node, $i) {
                    $next_current = $node->parents()->siblings()->filter('TH:contains("Location has (current):")');
                    $current_issues = $next_current->siblings()->eq($i)->text();
                    #$current_issues = $node->siblings()->eq(0)->text();
                    return array($node->siblings()->eq(0)->text(), $current_issues);

                }
            );
            #$holdings = $this->crawler->filterXPath('//TR/TH[contains("Location has (current):")/following-sibling::TD');
        }
        */
        $num_holdings = $this->crawler->filter('TH:contains("Location:")')->count();
        #print_r($this->crawler->filter('TR TH:contains("Location has (current):")')->eq(0)->text());
        $serial_holdings['number'] = $num_holdings;

        return $serial_holdings;
    }
}