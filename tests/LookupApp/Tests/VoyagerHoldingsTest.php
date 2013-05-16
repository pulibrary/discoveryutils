<?php
/**
 * Test Voyager Holdings via HTML extracted data
 */

namespace LookupApp\Tests;
use Voyager\Record;

class VoyagerHoldingsTest extends \PHPUnit_Framework_TestCase {

    protected function setUp() {
        $brief_holdings_html_on_order_response = file_get_contents(dirname(__FILE__).'../../../support/on_order_voyager.html');
        $this->voyager_on_order_record = new \Voyager\Record($brief_holdings_html_on_order_response);
        $serial_with_current_print_holdings = file_get_contents(dirname(__FILE__).'../../../support/serial_with_current_print_holdings.html');
        $this->serial_with_current_issues = new \Voyager\Record($serial_with_current_print_holdings);
    }

    function testGetVoyagerHoldings() {

    }

    function testIsOnOrderStatus() {
        $this->assertTrue($this->voyager_on_order_record->isOnOrder());

    }

    function testHasCopyCurrentStatus() {
        $this->assertInternalType('array', $this->voyager_on_order_record->getOnOrderMessage());
        $order_messages = $this->voyager_on_order_record->getOnOrderMessage();
        $this->assertEquals("1 Copy Ordered as of 05-08-13", $order_messages[0]);
    }

}