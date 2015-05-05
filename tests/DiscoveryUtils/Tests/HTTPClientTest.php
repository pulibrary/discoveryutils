<?php
namespace LookupApp\Tests;

use GuzzleHttp\Client;
//use Guzzle\Http\Client;

/*
 * Test should pass if you are running code from machine
 * authorized to use Primo Web Services. Check back office to 
 * authorize a new machine. Must have fixed IP unfortunately. 
 */

class HTTPClientTest extends  \PHPUnit_Framework_TestCase
{
    protected function setUp() {
       $this->primo_xservice = new \GuzzleHttp\Client("http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief");
    }
    
    
    function testPrimoBasicQuery() {
      $response = $this->primo_xservice->get("?institution=PRN&onCampus=false&indx=1&dym=true&highlight=true&displayField=title&query=title,exact,journal+of+politics&bulkSize=3&loc=local,scope:(PRN)")->send();
      //echo (string)$response->getBody();
    }
    
    function testPrimoFacetQuery() {
      $request = $this->primo_xservice->get('GET', "?institution=PRN&onCampus=false&indx=1&dym=true&highlight=true&displayField=title&bulkSize=3&loc=local,scope:(PRN)");
      $query_args = array('title,exact,journal+of+politics', 'facet_rtype,exact,journals');
      $query_facet_aggregator = new \Guzzle\Http\QueryAggregator\DuplicateAggregator(); // use this to allow duplicate key values 
      $request->getQuery()->setAggregator($query_facet_aggregator);
      $duplication_args = $query_facet_aggregator->aggregate("query", $query_args,  $request->getQuery());
      $request->getQuery()->add('query', $query_args);
      $response = $request->send();
    }
    
    
}
