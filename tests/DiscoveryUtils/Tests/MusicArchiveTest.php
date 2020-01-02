<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Xpath\Assert as XpathAssertions;
use GuzzleHttp\Client as HttpClient;
use DiscoveryUtils\Controller\MusicArchiveController;

class MusicArchiveTest extends WebTestCase {

  public function testHasValidCourseID() {
    $client = static::createClient();
    $client->request('GET', '/musicarchive/155');
    $this->assertEquals(200, $client->getResponse()->getStatusCode());
  }

  public function testInValidCourseID() {
    $client = static::createClient();
    $client->request('GET', '/musicarchive/FOO');
    $this->assertEquals(404, $client->getResponse()->getStatusCode());
  }
}
