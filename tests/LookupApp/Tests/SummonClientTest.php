<?php 

namespace LookupApp\Tests;


class SummonClientTest extends \PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $this->summon_connection = array(
      'client.id' => "princeton",
     'authcode' => 'LOIYKyKZbRiV0OVu9+worZW4ah'
    );
    $this->summon_client = new \Summon\Summon($this->summon_connection['client.id'], $this->summon_connection['authcode']);
    $this->sample_id = "FETCH-LOGICAL-c7701-c707b8b9148ce306134b08c9377abf7fe8220bc99f6d722b1e28cfda5ca82c450";
  }
  
  function testClientSetup() 
  {
    $this->assertTrue($this->summon_client instanceof \Summon\Summon);
  }
  
  function testRecordIDLookup() {
    $id_document = $this->summon_client->getRecord($this->sample_id);
    //print_r($id_document);
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
    $this->assertEquals(count($result_set['documents']), 3);
    
  }

  function testLimitToOnlyLibraryHoldings() {
    
    $result_set = $this->summon_client->query("Einstein");
    $this->summon_client->limitToHoldings();
    $local_result_set = $this->summon_client->query("Einstein");
    
    $this->assertGreaterThan($local_result_set['recordCount'], $result_set['recordCount']);
    $this->assertLessThan($result_set['recordCount'], $local_result_set['recordCount']);
    
  }

  

}  
  
  
  
  
