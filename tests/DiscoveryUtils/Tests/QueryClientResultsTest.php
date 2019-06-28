<?php

namespace DiscoveryUtils\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests "Summon Query API"
 */

 
class QueryClientResultsTest extends WebTestCase 
{
 
  public function createApplication() {
    $app = require __DIR__.'/../../../src/app.php';
    $app['debug'] = true;
    unset($app['exception_handler']);

    return $app;
  } 
  
  public function testBasicSummonSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/articles/any?query=music');
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testSummonTitleSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/articles/title?query=music');
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testSummonGuideSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/articles/guide?query=music');
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testSummonCreatorSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/articles/creator?query=music');
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testSummonIssnSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/articles/issn?query=music');
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testSummonIsbnSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/articles/isbn?query=music');
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testSummonSpellingSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/articles/spelling?query=music');
    $this->assertTrue($client->getResponse()->isOk());
  }

  public function testSummonRecommendationsSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/articles/recommendations?query=music');
    $this->assertTrue($client->getResponse()->isOk());
  }

 public function testBasicPulfaSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/pulfa/any?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }

 public function testTitlePulfaSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/pulfa/title?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }

 public function testCreatorPulfaSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/pulfa/creator?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }

 public function testGuidesSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/guides/any?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }

 public function testGuidesTitleSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/guides/title?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }

 public function testFaqSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/faq/get?query=music');
    $this->assertTrue($client->getResponse()->isOk());
    $response = $client->getResponse();
    $responseData = json_decode($response->getContent(), true);
    $this->assertGreaterThanOrEqual($responseData["number"], 2);   
 }

 public function testFaqOptionsSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/faq/options?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }

 public function testPudlSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/pudl/any?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }

 public function testBasicPrimoSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/pulsearch/any?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }
  
 public function testEmptyQuery() {
   $client = $this->createClient();
   $crawler = $client->request('GET', '/pulsearch/any?query=');
   $this->assertTrue($client->getResponse()->isOk());
 } 
 
 public function testNoQueryParam() {
   $client = $this->createClient();
   $crawler = $client->request('GET', '/pulsearch/any');
   $this->assertTrue($client->getResponse()->isOk());
 } 

 public function testDpulsearchSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/dpulsearch/any?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }
 public function testDpulsearchIssnSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/dpulsearch/issn?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }
 public function testDpulsearchIsbnSearch() {
  $client = $this->createClient();
  $crawler = $client->request('GET', '/dpulsearch/isbn?query=music');
  $this->assertTrue($client->getResponse()->isOk());
}
public function testDpulsearchTitleSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/dpulsearch/title?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }

 public function testMapSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/mapsearch/any?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }
 public function testMapIssnSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/mapsearch/issn?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }
 public function testMapIsbnSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/mapsearch/isbn?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }
 public function testMapTitleSearch() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/mapsearch/title?query=music');
    $this->assertTrue($client->getResponse()->isOk());
 }
 public function testArtSearch() {
   $client = $this->createClient();
   $client->request('GET', '/arts/all?query=music');
   $this->assertTrue($client->getResponse()->isOk());
   $response = $client->getResponse();
   $responseData = json_decode($response->getContent(), true);
   $this->assertGreaterThanOrEqual($responseData["number"], 0);
 }
 public function testArtArtobjectsSearch() {
   $client = $this->createClient();
   $client->request('GET', '/arts/artobjects?query=gogh');
   $this->assertTrue($client->getResponse()->isOk());
   $response = $client->getResponse();
   $responseData = json_decode($response->getContent(), true);
   $this->assertGreaterThanOrEqual($responseData["number"], 68);
 }
//  TODO: Do we really want makers?  there is no data beyond an id
 public function testArtMakersSearch() {
   $client = $this->createClient();
   $client->request('GET', '/arts/makers?query=gogh');
   $this->assertTrue($client->getResponse()->isOk());
   $response = $client->getResponse();
   $responseData = json_decode($response->getContent(), true);
   $this->assertGreaterThanOrEqual($responseData["number"], 6);
 }
 public function testArtPackagesSearch() {
   $client = $this->createClient();
   $client->request('GET', '/arts/packages?query=gogh');
   $this->assertTrue($client->getResponse()->isOk());
   $response = $client->getResponse();
   $responseData = json_decode($response->getContent(), true);
   $this->assertGreaterThanOrEqual($responseData["number"], 0);
 }

}

