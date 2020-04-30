<?php
namespace DiscoveryUtils\Tests;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Xpath\Assert as XpathAssertions;
use GuzzleHttp\Client as HttpClient;

class LookupTest extends WebTestCase
{
  public function createApplication() {
    $app = require __DIR__.'/../../../src/app.php';
    $app['debug'] = true;
    unset($app['exception_handler']);

    return $app;
  }

  public function testIndex() {
    $client = static::createClient();
    // $client = $this->createClient();
    $crawler = $client->request('GET', '/');
    $this->assertTrue($crawler->filter('html:contains("Discovery")')->count() > 0);
  }

  public function testHours() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/hours');
    $this->assertTrue($crawler->filterXPath('//locations/location/eventsFeedConfig')->count() > 0);
  }

  public function testDays() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/hours/rbsc');
    $response = $client->getResponse();
    $responseData = json_decode($response->getContent(), true);
    $this->assertContains("Open", $responseData["mudd-hours"] );
  }
}
