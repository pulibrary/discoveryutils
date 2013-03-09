<?php
namespace Primo;
use Primo\Query;
use Guzzle\Service\Client as HttpClient;
use Guzzle\Http\QueryAggregator\DuplicateAggregator as DuplicateAggregator;

class Client
{
  
  private $xservice_base = "/PrimoWebServices";
  private $xservice_brief_search = "xservice/search/brief?"; //run a primo search
  private $xservice_getit = "xservice/getit?"; // for straight ID lookups 
  private $scopelist = "xservice/getscopesofview?";
  private $institution;
  public $client;
  
  
  function __construct($primo_server_connection) {
    $this->institution = $primo_server_connection['institution'];
    $this->default_scope = $primo_server_connection['default_view_id'];
    $this->client = new HttpClient($primo_server_connection['base_url'].$this->xservice_base);
  }
  
  public function getID($pnx_id) {
    $response = $this->client->get($this->xservice_getit . "institution=" . $this->institution ."&docId=".$pnx_id)->send();
    if(strlen($response) != 0) { 
      return (string)$response->getBody(); //also can do $response->getBody(TRUE)
    } else {
      return false;
    }
  }
  
  public function getHttpClient() {
    return $this->client;
  }
  
  public function getInstitution() {
    return $this->institution;
  }
  
  /*
   * xservice URL like http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false&query=any,exact,lok&indx=1&bulkSize=50&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=50
   * 
   * send item a primo query object 
   * should I have a primo results objects 
   */
  public function doSearch(\Primo\Query $query) {
    //echo $this->xservice_brief_search . 
    //echo $query->getQueryString();
    $request = $this->client->get($this->xservice_brief_search . $query->getQueryString());
    
    $query_facet_aggregator = new DuplicateAggregator(); // use this to allow duplicate key values 
    $request->getQuery()->setAggregator($query_facet_aggregator);
    if($query->hasFacets()) {
      
      foreach($query->getFacets() as $facet) {
        $request->getQuery()->add('query', $facet);
      }
        
    }

    //$request->getQuery()->setAggregateFunction(array($request->getQuery(), 'aggregateUsingDuplicates'));
    $response = $request->send();

    if(strlen($response) != 0) { 
      return (string)$response->getBody(); 
    } else {
      return false;
    }
  }

  /* obtain the list of currently available Primo scopes */
  public function getScopes() {
    $request = $this->client->get($this->scopelist . "viewId=" . $this->default_scope);
    $response = $request->send();
    if(strlen($response) != 0) { 
      return (string)$response->getBody(); 
    } else {
      return false;
    }
  }
  

  /* not sure if this really even useful? */ 
  public function __toString() {
    return $this->client;;
  }
  
  
  
}
