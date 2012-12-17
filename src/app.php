<?php

//require_once __DIR__.'/../vendor/silex.phar';

use Silex\Application;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\Yaml\Yaml;
use Primo\Record as PrimoRecord,
    Primo\PermaLink as Permalink,
    Primo\Query as PrimoQuery,
    Primo\Client as PrimoClient,
    Primo\SearchDeepLink as SearchDeepLink,
    Primo\RequestClient as RequestClient,
    Primo\Response as PrimoResponse,
    Primo\ScopeList as PrimoScopeList;
use Summon\Summon,
    Summon\Query as SummonQuery,
    Summon\Response as SummonResponse;
use Pulfa\Pulfa,
    Pulfa\Response as PulfaResponse;

$app = new Silex\Application(); 

$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path'       => __DIR__.'/../views',
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile'       => __DIR__.'/../log/usage.log',
    'monolog.level'         => 'Logger::DEBUG'
));

$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => __DIR__.'/../cache/',
));

/* Define possible search tabs */
$app['search_tabs'] = array(
  array("index" => "location", "label" => "Catalog+ (Primo/Searchit)"),
  array("index" => "summon", "label" => "Articles + (Summon)"),
  array("index" => "course", "label" => "Course Reserves"),
  array("index" => "blended", "label" => "Catalog and Summon"),
);

$library_scopes = Yaml::parse(__DIR__.'/../conf/scopes.yml');

$app['primo_server_connection'] = array(
  'base_url' => 'http://searchit.princeton.edu',
  'institution' => 'PRN',
  'default_view_id' => 'PRINCETON',
  'default_pnx_source_id' => 'PRN_VOYAGER',
  'default.scope' => array("OTHERS","FIRE"),
  'default.search' => "contains",
  'num.records.brief.display' => 3,
  'available.scopes' => $library_scopes,
);



$app['summon.connection'] = Yaml::parse(__DIR__.'/../conf/summon.yml');
$app['pulfa'] = array(
  'host' => "http://findingaids.princeton.edu",
  'base' => "/collections.xml?"
);

$app['locator.base'] = "http://library.princeton.edu/catalogs/locator/PRODUCTION/index.php";
// get primo scopes via webservices http://searchit.princeton.edu/PrimoWebServices/xservice/getscopesofview?viewId=PRINCETON
$app['stackmap'] = Yaml::parse(__DIR__.'/../conf/stackmap.yml');

$app['locations.base'] = "http://libserv5.princeton.edu/requests/locationservice.php";


// set up a configured primo client to reuse throughout the project
$app['primo_client'] = $app->share(function ($app) {
    return new PrimoClient($app['primo_server_connection']);
});


$app['environment'] = Yaml::parse(__DIR__.'/../conf/environment.yml');

if ($app['environment']['env'] != "production") {
  $app['debug'] = true;
}

/* basic error catching */
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }
    switch ($code) {
        case 404:
            $message = 'The requested page could not be found.';
            break;
        default:
            $message = 'We are sorry, but something went terribly wrong.';
    }

    return new Response($message);
});


$app->get('/', function() use($app) {
  
   return $app['twig']->render('home.html.twig', array(
    'environment' => $app['environment']['env'], 
    'title' => $app['environment']['title']
  ));
});

/*
 * Redirect Route to Primo Deep Link for IDs
 */
$app->match('/show/{rec_id}', function($rec_id) use($app) {
    
  $primo_record_link = new PermaLink($rec_id, $app['primo_server_connection']);
  $app['monolog']->addInfo("REDIRECT: " . $primo_record_link->getLink());
  return $app->redirect($primo_record_link->getLink());
})->assert('rec_id', '^(PRN_VOYAGER|dedupmrg)\d+');

/* 
 * redirect route for primo basic searches 
 * tab should match an available primo search tab
 */
$app->match('/search/{tab}', function(Request $request, $tab) use($app) {

  $query = $app['request']->get('query'); //FIXME escaping this causes primo search to fail 
  if($app['request']->server->get('HTTP_REFERER')) {
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }

  if ($tab == "summon") {
    $deep_search_link = new SummonQuery($query, array(
      "s.cmd" => "addFacetValueFilters(ContentType,Newspaper+Article:t)",      
      "keep_r" => "true" )
    );
  } elseif($tab == "course") {
    $deep_search_link = new SearchDeepLink($query, "any", "contains", $app['primo_server_connection'], $tab, array("COURSE"));
  } elseif($tab == "blended") {
    $deep_search_link = new SearchDeepLink($query, "any", "contains", $app['primo_server_connection'], $tab, array("PRN", "SummonThirdNode"));
  } else {
    $deep_search_link = new SearchDeepLink($query, "any", "contains", $app['primo_server_connection'], $tab, array("OTHERS", "FIRE")); //WATCHOUT - Order Matters 
  }
  $app['monolog']->addInfo("TAB:" . $tab . "\tQUERY:" . $query . "\tREDIRECT:" . $deep_search_link->getLink() . "\tREFERER:" . $referer);
  return $app->redirect($deep_search_link->getLink());

});

$app->get('/record/{rec_id}.json', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  //$record_data;
  $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
  $stub_data = $primo_record->getBriefInfo();
  $app['monolog']->addInfo("PNXID_REQUEST: " . json_encode($stub_data));
  return new Response(json_encode($stub_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+'); //test regular expression validation of route 

$app->get('/record/{rec_id}.xml', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  return new Response($record_data, 200, array('Content-Type' => 'application/xml'));
})->assert('rec_id', '\w+'); 

$app->get('/record/{rec_id}.ris', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
  $ris_data = $primo_record->getCitation("RIS");
  $app['monolog']->addInfo("RIS_REQUEST: " . $rec_id . "\n" . $ris_data);
  return new Response($ris_data, 200, array('Content-Type' => 'application/x-research-info-systems'));
})->assert('rec_id', '\w+');


$app->get('/record/{rec_id}', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
  $stub_data = $primo_record->getBriefInfo();
  $response_data = array();
  $response_data['rec_id'] = $rec_id;
  $response_data['pnx_response'] = $stub_data;

  return $app['twig']->render('record.twig', $response_data);
})->assert('rec_id', '\w+');

/*
 * build a map for a given location code and id
 */

/* do not send dedup ids to this controller 
 * 
 * @params
 * id = Voyager Style Numeric ID (Maybe should accept either one)
 * loc = Voyager Location Code 
 * 
 * Example http://mydiscservice.edu/map?id=123456&loc=stax
 * 
 * */
$app->get('/map', function() use ($app) {
  $referer = "DIRECT";
  if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
    $referer = $app['request']->server->get('HTTP_REFERER');
  }
  $rec_id = $app->escape($app['request']->get("id")); //FIXME Should through an error if neither parameter is present
  $location_code = $app->escape($app['request']->get("loc"));
  if(preg_match('/^dedup/', $rec_id)) {
    $record_data = $app['primo_client']->getID($rec_id);
  } else {
    if(preg_match('/^\d+/', $rec_id)) {
      $requested_id = $app['primo_server_connection']['default_pnx_source_id'].$rec_id;
    } else {
      $requested_id = $rec_id;
    }
    $query = new PrimoQuery($requested_id, "any", "exact", $app['primo_server_connection']['default.scope']);
    $record_data = $app['primo_client']->doSearch($query);    

  }
  $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
   
  foreach($primo_record->getHoldings() as $holding) { //iterate through holdings objects 
    if($holding->location_code == $location_code) {
      if($holding->source_id == $requested_id) {
      	$holding_to_map = $holding;
        break; //why break from loop here? 
      } else {
        $holding_to_map = $holding;
      } 
    }
  }
  
  /*
   * ***Note on Empty Holdings*******
   * 
   * If there is no holding in Primo Web Services should treat the item differently depending on whether or not it came
   * from Voyager or Searchit. There can be discrepancies for temp holdings. 
   * 
   * For Searchit - generate an error. 
   * For Voyager - Just pass through to the PUL Locator for an item not a stackmap eligible library
   * 
   */
  
  if(!(isset($holding_to_map))) {
    // most likely for temp/perm location mismatches
    // send these over to the locator  
    $app['monolog']->err("TYPE:No Holdings Available for Requested Record ID\tREC_ID:$rec_id\tLOCATION:$location_code\tREFERER:$referer"); //log the error
    
    $map_params = array(
        'loc' => $location_code,
        'id' => $rec_id,
      );
    $map_url = $app['locator.base'] . "?" . http_build_query($map_params);
    
    $app['monolog']->addInfo("MAP:$map_url\tLOCATION:$location_code\tRECORD:$rec_id"); 
   
    return $app->redirect($map_url);
    
  } elseif(in_array($holding_to_map->location_code, $app['stackmap']['reserve.locations'])) {
    $location_info = json_decode(file_get_contents($app['locations.base'] . "?" . http_build_query(array('loc' => $holding_to_map->location_code))), TRUE); //FIXME
    return $app['twig']->render('reserve.twig', array(
       'record_id' => $rec_id,
       'title' => $primo_record->getNormalizedTitle(),
       'call_number' => $holding_to_map->call_number,
       'library' => $location_info[$holding_to_map->location_code]['libraryDisplay'],
       'location_label' => $location_info[$holding_to_map->location_code]['collectionDisplay']
       ));
  } else {
      
    if(in_array($holding_to_map->primo_library, $app['stackmap']['eligible.libraries'])) { //FIXE
       /*
       * get the location display Name from locations service because stack map wants it that way
       * should be obtained via a database call in future when apps mere 
       */ 
      $location_info = json_decode(file_get_contents($app['locations.base'] . "?" . http_build_query(array('loc' => $holding_to_map->location_code))), TRUE); //FIXME
      //print_r($location_info);
      if(in_array($holding_to_map->location_code, $app['stackmap']['by.title.locations'])) {
        $call_number = $primo_record->getNormalizedTitle();
      } else {
        $call_number = $holding_to_map->call_number;
      }
      $map_params = array(
        'callno' => $call_number,
        'location' => $holding_to_map->location_code,
        'library' => strval($location_info[$holding_to_map->location_code]['libraryDisplay']),
      );
      $map_url = $app['stackmap']['base.url'] . "?" . http_build_query($map_params);
    } else {
      
      $map_params = array(
        'loc' => $holding_to_map->location_code,
        'id' => $rec_id,
      );
      $map_url = $app['locator.base'] . "?" . http_build_query($map_params);
    }
    $app['monolog']->addInfo("MAP:$map_url\tLOCATION:$location_code\tRECORD:$rec_id"); 
   
    return $app->redirect($map_url);
    
  } 
});



/*
 * return all links associated with a given primo id
 */
$app->get('/links/{rec_id}.json', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
  $all_links_data = $primo_record->getAllLinks();
  return new Response(json_encode($all_links_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

/*
 * Return all PUL locations associated with a given primo id
 */
$app->get('/locations/{rec_id}.json', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data, $app['primo_server_connection']);
  $all_links_data = $primo_record->getAvailableLibraries();
  if ($view_type = $app['request']->get('view')) { // this method may be slow per symfony request class docs
    $all_links_data['view'] =  $view_type; 
  }
  return new Response(json_encode($all_links_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

$app->get('/availability/{rec_id}.json', function($rec_id) use($app) {
  $availability_client = new RequestClient($app->escape($rec_id));
  $availability_response = $availability_client->doLookup();
  $app['monolog']->addInfo("Request Lookup: " . $availability_client);
  return new Response($availability_response, 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

$app->get('/availability/{rec_id}', function($rec_id) use($app) {
  $availability_client = new RequestClient($app->escape($rec_id));
  $availability_response = $availability_client->doLookup();
  $app['monolog']->addInfo("Request Lookup: " . $availability_client);
  
  return $app['twig']->render('availability.twig', array(
    'record_id' => $rec_id, 
    'ava_response' => $availability_response
  ));
})->assert('rec_id', '\w+');

/*
 * Generic "services" route to all for querying of specific primo services
 * Returns values for {locations, openurl, fulltext, delivery, borrowdirect }
 */
$app->get('/{rec_id}/{service_type}.{format}', function($rec_id, $service_type, $format="html") use ($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id)); //FIXME perhaps try and use the symfony validator utility to filter all rec_ids and service_types
  $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
  // decide which service type to use
  $location_links_data = $primo_record->getAllLinks();
  if ($format == "json") {
    return new Response(json_encode($location_links_data), 200, array('Content-Type' => 'application/json'));
  }
})->assert('rec_id', '\w+');

$app->get('/scopelist', function() use ($app) {
  $scope_list_response = $app['primo_client']->getScopes();
  $scope_list = new PrimoScopeList($scope_list_response);
  $yaml = $scope_list->asYaml();
  //updat yaml file
  file_put_contents(__DIR__.'/../conf/scopes.yml', $yaml);
  
  return new JsonResponse($scope_list->getScopes());
});

$app->get('/locations', function() use ($app) {
    
  $locations = json_decode(file_get_contents("http://libserv5.princeton.edu/requests/locationservice.php"), TRUE);
  ksort($locations);
  file_put_contents(__DIR__.'/../conf/locations.json', json_encode($locations));
  
  return new JsonResponse($locations);
  
});


/*
 * Route to direct queries to Pulfa
 * 
 */

$app->get('/pulfa/{index_type}', function($index_type) use($app) {
  if($app['request']->get('query')) {
    $query = $app['request']->get('query');
  } else {
    return "No Query Supplied";
  }
  if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }
  
  $pulfa = new \Pulfa\Pulfa($app['pulfa']['host'], $app['pulfa']['base']);
  $pulfa_response_data = $pulfa->query($query, 0, 3);
  $pulfa_response = new PulfaResponse($pulfa_response_data, $query);
  $brief_response = $pulfa_response->getBriefResponse();
  $brief_response['query'] = $app->escape($query);
  
  $app['monolog']->addInfo("Pulfa Query:" . $query . "\tREFERER:" . $referer);
  return new Response(json_encode($brief_response), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
})->assert('index_type', '(title|any|creator)'); 
 
/*
 * Route to direct queries to Summon API
 * 
 */ 

$app->get('/articles/{index_type}', function($index_type) use($app) {
  if($app['request']->get('query')) {
    $query = $app['request']->get('query');
  } else {
    return "No Query Supplied";
  }
  //return "Articles Query " . $app->escape($query);
  if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }
  
  $summon_client = new Summon($app['summon.connection']['client.id'], $app['summon.connection']['authcode']);
  $summon_client->limitToHoldings(); // only bring back Princeton results

  if($index_type == 'guide') { //FIXME Only Libguides 
    $summon_client->addFilter('ContentType, Research Guide');
    $summon_data = new SummonResponse($summon_client->query($query, 1, 3));
    $response_data = array(
      'query' => $app->escape($query),
      'number' => $summon_data->hits,
      'more' => $app['summon.connection']['base.url'] . $summon_data->deep_search_link,
      'records' => $summon_data->getBriefResults(),
    );
  } elseif ($index_type == "spelling") {
    if($summon_client->checkSpelling($query, 1, 1)) {
      $suggestion = $summon_client->checkSpelling($query, 1, 1);
    }
    if(isset($suggestion)) {
      $response_data = array($suggestion);
    } else {
      $response_data = array();
    }
  } elseif($index_type == "recommendations") {
    $summon_data = new SummonResponse($summon_client->query($query, 1, 3));
    $response_data['recommendations'] = $summon_data->getRecommendations();
    $response_data['number'] = count($response_data['recommendations']);
  } else {
    $summon_client->addCommandFilter("addFacetValueFilters(ContentType,Newspaper+Article:t)"); //FIXME this shoudl default to exclude and retain filter to remove newspapers
    $summon_data = new SummonResponse($summon_client->query($query, 1, 3)); 
    //print_r($summon_data);
    $summon_full_search_link = new SummonQuery($query, array(
      "s.cmd" => "addFacetValueFilters(ContentType,Newspaper+Article:t)",      
      "keep_r" => "true" )
    );
    $response_data = array(
      'query' => $app->escape($query),
      'number' => $summon_data->hits,
      'more' => $summon_full_search_link->getLink(),
      'records' => $summon_data->getBriefResults(),
    );
    //print_r($summon_data->deep_search_link);
  }
  
  
  
  $app['monolog']->addInfo("Summon $index_type Query:" . $query . "\tREFERER:" . $referer);
  return new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
})->assert('index_type', '(any|title|guide|creator|issn|isbn|spelling|recommendations)');



/* Wrapper for Primo Brief Search API Call
 * 
 * EL Commons http://www.exlibrisgroup.org/display/PrimoOI/Brief+Search
 * 
 * Route Variable 
 * 
 * {index_type} scope the search to a particular field
 * choices issn|isbn|lccn|oclc|title|any|lsr05|creator
 * 
 * lsr05 is call number
 * 
 * Possible Query Parameters
 * 
 * @query - string to search for
 * 
 * @limit - can be contains, exact, or begins_with see $app['primo_server_connection']['default.search'] for default
 * NOTE "begins_with" can only be used with the title parameter otherwise an error can be thrown
 *
 * @scopes - see $app['primo_server_connection']['default.scope'] for default value
 * Example: .....?scopes=ENG,MUSIC - search only english and music libraries
 * 
 * @format - see $app['primo_server_connection']['default.search'] for default value
 * Example: /find/title/journal+of+politics?format=journals - get only items with the journals facet back
 * 
 */

 
 $app->get('/find/{index_type}', function($index_type) use($app) {

  if($app['request']->get('query')) {
    $query = $app['request']->get('query');
  } else {
    return "No Query Supplied";
  }
  
  if($app['request']->server->get('HTTP_REFERER')) {
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }
  if($app['request']->get('scopes')) {
    $scopes = explode(",", $app['request']->get('scopes'));  
  } else {
    $scopes = $app['primo_server_connection']['default.scope'];
  }
  if($app['request']->get('limit')) {
    $operator = $app->escape($app['request']->get('limit'));
  } else {
    $operator = $app['primo_server_connection']['default.search'];
  }
  if($app['request']->get('format')) {
    $format_facet = "facet_rtype,exact," . $app['request']->get('format');
  }

  if($app['request']->get('subject')) {
    $subject_facet = "facet_topic,exact," . $app['request']->get('subject');
  }

  $primo_query = new PrimoQuery($query, $app->escape($index_type), $operator, $scopes, $app['primo_server_connection']['num.records.brief.display']);
  if(isset($format_facet)) {
    $primo_query->addFacet($format_facet);
  }
  if(isset($subject_facet)) {
    $primo_query->addFacet($subject_facet); 
  }
  $search_results = $app['primo_client']->doSearch($primo_query);
  if($search_results) {
    $response = new PrimoResponse($search_results, $app['primo_server_connection']);
    $deep_link = new SearchDeepLink($query, $app->escape($index_type), $operator, $app['primo_server_connection'], 'location', array("OTHERS", "FIRE"), $primo_query->getFacets());
    $response_data = array(
      'query' => $app->escape($query),
      'number' => $response->getHits(),
      'more' => $deep_link->getLink(),
      'records' => $response->getBriefResults(),
      );
    $app['monolog']->addInfo("Index Query:" . $query . "\tREFERER:" . $referer);
  } else {
    $response_data = array("no results available at this time");
  }
  return new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public',));
})->assert('index_type', '(issn|isbn|lccn|oclc|title|any|lsr05|creator)'); // should this be a list of possible options from the 

return $app;
