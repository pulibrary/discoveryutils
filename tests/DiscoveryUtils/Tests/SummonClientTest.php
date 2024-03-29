<?php 

namespace DiscoveryUtils\Tests;


class SummonClientTest extends \PHPUnit\Framework\TestCase {
  
  protected function setUp(): void {
    $this->summon_connection = array(
      'client.id' => "princeton",
      'authcode' => $_ENV['SUMMON_AUTHCODE']
    );
    $this->summon_client = new \Summon\Summon($this->summon_connection['client.id'], $this->summon_connection['authcode']);
    $this->sample_id = "FETCH-crossref_primary_10_1038_nature_2014_157593";
  }
  
  function testClientSetup() 
  {
    $this->assertTrue($this->summon_client instanceof \Summon\Summon);
  }
  
  function testRecordIDLookup() {
    $id_document = $this->summon_client->getRecord($this->sample_id);
    $this->assertEquals($id_document['documents'][0]['ID'][0], $this->sample_id);
    $this->assertEquals(count($id_document['documents']), 1);
  }
  
  function testSummonQueryLookup() {
    $result_set = $this->summon_client->query("Mike Ditka");
    $this->assertEquals(count($result_set['documents']), 20); //default result set returned by summon is 20
  }
  
  function testResultLimitRecordsToReturn() {
    $results_page = 1;
    $num_results_to_return = 3;
    $result_set = $this->summon_client->query("True Blood", $results_page, $num_results_to_return);
    //print_r($result_set);
    $this->assertEquals(count($result_set['documents']), 3);
    
  }

  function testLimitToOnlyLibraryHoldings() {
    $this->summon_client->limitToHoldings(false);
    $result_set = $this->summon_client->query("Einstein");
    $this->summon_client->limitToHoldings(true);
    $local_result_set = $this->summon_client->query("Einstein");
    
    $this->assertGreaterThan($local_result_set['recordCount'], $result_set['recordCount']);
    $this->assertLessThan($result_set['recordCount'], $local_result_set['recordCount']);
    
  }
  
  function testExcludeNewspaperFromResultSet() {
    $this->summon_client->limitToHoldings(true);
    $result_w_newspaper = $this->summon_client->query("Mark Twain");
    $this->assertGreaterThan(240000, $result_w_newspaper['recordCount']);
    //$this->summon_client->addFilter("ContentType,Newspaper+Article,t");
    $this->summon_client->addCommandFilter("addFacetValueFilters(ContentType,Newspaper+Article:true)");
    $result_without_newspaper = $this->summon_client->query("Mark Twain");
    $this->assertLessThan(1000000, $result_without_newspaper['recordCount']);
  }

  

}  
  
  
  
  
