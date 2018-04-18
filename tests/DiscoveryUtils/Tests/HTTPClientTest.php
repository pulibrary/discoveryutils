<?php
namespace DisocoveryUtils\Tests;

use GuzzleHttp\Client;

/*
 * Test should pass if you are running code from machine
 * authorized to use Primo Web Services. Check back office to 
 * authorize a new machine. Must have fixed IP unfortunately. 
 */

class HTTPClientTest extends  \PHPUnit\Framework\TestCase
{
    protected function setUp() {
       $this->primo_xservice = new \GuzzleHttp\Client(['base_url' => "http://princeton-primo.hosted.exlibrisgroup.com/PrimoWebServices/xservice/search/brief"]);
    }
    
    
    function testPrimoBasicQuery() {
      $response = $this->primo_xservice->get("?institution=PRN&onCampus=false&indx=1&dym=true&highlight=true&displayField=title&query=title,exact,journal+of+politics&bulkSize=3&loc=local,scope:(PRN)");
    }
    
    // function testPrimoFacetQuery() {
    //   $request = $this->primo_xservice->get("?institution=PRN&onCampus=false&indx=1&dym=true&highlight=true&displayField=title&bulkSize=3&loc=local,scope:(PRN)");
    //   $query_args = array('title,exact,journal+of+politics', 'facet_rtype,exact,journals');
    //   $search_query =  $request->getQuery();
    //   $search_query->setAggregator($search_query::duplicateAggregator());
    //   $response = $this->client->send($request);
    //   // $duplication_args = $query_facet_aggregator->aggregate("query", $query_args,  $request->getQuery());
    //   // $request->getQuery()->add('query', $query_args);
    //   $response = $request->send();
    // }
    
    
}
