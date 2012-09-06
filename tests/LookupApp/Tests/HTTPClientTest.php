<?php
namespace LookupApp\Tests;

use Guzzle\Service\Client;
//use Guzzle\Http\Client;

/*
 * Test should pass if you are running code from machine
 * authorized to use Primo Web Services. Check back office to 
 * authorize a new machine. Must have fixed IP unfortunately. 
 */

class HTTPClientTest extends  \PHPUnit_Framework_TestCase
{
    protected function setUp() {
       //$this->client = new \Guzzle\Service\Client('http://libwebprod.princeton.edu/searchit');
       $this->primo_xservice = new \Guzzle\Service\Client("http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief");
    }
    
    function testBaseURLSetting() {
      //$response = $this->client->get('find/any/cats?limit=exact')->send();
      //echo $response->getBody();
    }
    
    function testPrimoBasicQuery() {
      $response = $this->primo_xservice->get("?institution=PRN&onCampus=false&indx=1&dym=true&highlight=true&displayField=title&query=title,exact,journal+of+politics&bulkSize=3&loc=local,scope:(PRN)")->send();
      //echo (string)$response->getBody();
    }
    
    function testPrimoFacetQuery() {
      $request = $this->primo_xservice->get("?institution=PRN&onCampus=false&indx=1&dym=true&highlight=true&displayField=title&query=title,exact,journal+of+politics&bulkSize=3&loc=local,scope:(PRN)");
      $request->getQuery()->add('query', 'facet_rtype,exact,journals');
      $request->getQuery()->setAggregateFunction(array($request->getQuery(), 'aggregateUsingDuplicates'));
      $response = $request->send();
    }
    
    function testFileGetContents() {
      $base = "http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?";
      //$without_facet = file_get_contents($base . "institution=PRN&onCampus=false&indx=1&dym=true&highlight=true&displayField=title&query=title,exact,journal+of+politics&bulkSize=3&loc=local,scope:(PRN)");
      //print_r($without_facet);
      //$with_facet = file_get_contents($base . "institution=PRN&onCampus=false&indx=1&dym=true&highlight=true&displayField=title&query=title,exact,journal+of+politics&bulkSize=3&loc=local,scope:(PRN)&query=facet_rtype,exact,jouranls");
      //print_r($with_facet);
    }
    
}
