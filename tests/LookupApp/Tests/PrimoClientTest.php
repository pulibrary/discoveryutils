<?php

namespace LookupApp\Tests;

/**
 * isbn xservice
 * http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false&query=isbn,exact,1416987037&indx=1&bulkSize=1&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=1
 * facet xerservice
 * 
 * Compound Query: 
 * http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false
 * &indx=1&bulkSize=1&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=1
 * 
 */
class LookupPrimoClientTest extends \PHPUnit_Framework_TestCase {
  
  function testBaseURLSetting() {
    
  }
  
  /*
   * Test the primo "getit" service
   */
  
  function testPrimoSingleRecordRequest() {
    
  }
  
  /*
   * Test the primo "breif" service
   */
  
  function testRunPrimoBasicQuery() {
    
  }
  
  /*
   * Test query with compound statements
   */
  
  function testRunPrimoCompoundBasicQuery() {
    
  }
  
  /*
   * Test query with string query plus facet query
   */
  
  function testRunPrimoFacetQuery() {
    
  }
  
  /*
   * Test query with sring plus a library "scope"
   */
  
  function testRunPrimoScopedQuery() {
    
  }
  
}