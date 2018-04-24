<?php
/**
 * Test Voyager Holdings via HTML extracted data
 */

namespace DiscoveryUtils\Tests;
use Voyager\Record;

class VoyagerHoldingsTest extends \PHPUnit\Framework\TestCase {

    protected function setUp() {
        $brief_holdings_html_on_order_response = file_get_contents(dirname(__FILE__).'../../../support/on_order_voyager.html');
        $this->voyager_on_order_record = new \Voyager\Record($brief_holdings_html_on_order_response);
        $serial_with_current_print_holdings = file_get_contents(dirname(__FILE__).'../../../support/serial_with_current_print_holdings.html');
        $this->serial_with_current_issues = new \Voyager\Record($serial_with_current_print_holdings);
    }


    function testHasCurrentSerialHoldings() {
        $this->assertTrue($this->serial_with_current_issues->hasCurrentSerials());
    }

    function testGetCurrentSerialHoldings() {
        $this->assertInternalType('array', $this->serial_with_current_issues->getCurrentSerialHoldings());
        $current_holdings = $this->serial_with_current_issues->getCurrentSerialHoldings();
        $this->assertEquals(5, $current_holdings['number']);
        $this->assertEquals(5, count($current_holdings['values']));
    }

    function testGetLocations() {
        $this->assertInternalType('array', $this->serial_with_current_issues->getLocations());
        $current_holdings = $this->serial_with_current_issues->getLocations();
        $this->assertEquals(10, $current_holdings['number']);
        //$this->assertEquals(10, count($current_holdings['values']));
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