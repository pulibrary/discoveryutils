<?php
namespace Primo;
use Primo\Query;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\QueryAggregator\DuplicateAggregator as DuplicateAggregator;

class Primo
{
  
  private $xservice_base = "PrimoWebServices/";
  private $xservice_brief_search = "xservice/search/brief?"; //run a primo search
  private $xservice_getit = "xservice/getit?"; // for straight ID lookups 
  private $scopelist = "xservice/getscopesofview?";
  private $institution;
  public $client;
  
  
  function __construct($primo_server_connection, HttpClient $client = null) {
    $this->institution = $primo_server_connection['institution'];
    $this->default_scope = $primo_server_connection['default_view_id'];
    if ( $client != null )
    {
      $this->http_client = $client;
    }
    else 
    {
      $this->client = new HttpClient(['base_url' => $primo_server_connection['base_url']]);
    }
  }
  
  public function getID($pnx_id, $json = null) {
    $query = array(
      "institution" => $this->institution,
      "docId" => $pnx_id
      );
    if (!is_null($json)) {
      $query['json'] = 'true';
    }
    $response = $this->client->get($this->xservice_base . $this->xservice_getit, [
      'query' => $query,
      'timeout' => 5 ]
    );

    $status = $response->getStatusCode();
    if($status == 200) {
      return (string)$response->getBody();
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
    
    $request = $this->client->createRequest('GET', 
      $this->xservice_base . $this->xservice_brief_search . $query->getQueryString(),
      [ 'timeout' => 5 ]
      );
    
    //$query_facet_aggregator = new DuplicateAggregator(); // use this to allow duplicate key values 
    $search_query =  $request->getQuery();
    $search_query->setAggregator($search_query::duplicateAggregator());
    if($query->hasFacets()) {
      
      foreach($query->getFacets() as $facet) {
        $search_query['query'][] = $facet;
      }
        
    }
    //echo $request->getURL();
    //$request->getQuery()->setAggregateFunction(array($request->getQuery(), 'aggregateUsingDuplicates'));
    $response = $this->client->send($request);
    //echo $response->getBody();
    $status = $response->getStatusCode();
    if($status == 200) { 
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
