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
}