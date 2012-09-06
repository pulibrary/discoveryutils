<?php

//require_once __DIR__.'/../vendor/silex.phar';

use Silex\Application;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Yaml\Yaml;
use PrimoServices\PrimoRecord,
    PrimoServices\PrimoClient,
    PrimoServices\PermaLink,
    PrimoServices\SummonQuery,
    PrimoServices\PrimoQuery,
    PrimoServices\RequestClient,
    PrimoServices\SearchDeepLink,
    PrimoServices\PrimoResponse;
use Summon\Summon,
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

$app['primo_server_connection'] = array(
  'base_url' => 'http://searchit.princeton.edu',
  'institution' => 'PRN',
  'default_view_id' => 'PRINCETON',
  'default_pnx_source_id' => 'PRN_VOYAGER',
  'default_scope' => array("OTHERS","FIRE"),
  'default.search' => "contains",
  'num.records.brief.display' => 3
);



$app['summon.connection'] = Yaml::parse(__DIR__.'/../conf/summon.yml');
$app['pulfa'] = array(
  'host' => "http://findingaids.princeton.edu",
  'base' => "/collections.xml?"
);

$app['locator.base'] = "http://library.princeton.edu/catalogs/locator/PRODUCTION/index.php";
// get primo scopes via webservices http://searchit.princeton.edu/PrimoWebServices/xservice/getscopesofview?viewId=PRINCETON
$app['stackmap.base'] = "http://princeton.stackmap.com/view/";
$app['stackmap.eligible.libraries'] = array(
  "ARCH",
  "EAL",
  "ENG",
  "LEWIS",
  "MUSIC",
  "MARQ",
  "STOKES",
  "PPL",
);
$app['locations.base'] = "http://libserv5.princeton.edu/requests/locationservice.php";


// set up a configured primo client to reuse throughout the project
$app['primo_client'] = $app->share(function ($app) {
    return new PrimoClient($app['primo_server_connection']);
});




$app['debug'] = true;


$app->get('/', function() use($app) {
  return 'Primo Lookup App';
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
 * */
$app->get('/map', function() use ($app) {
  $rec_id = $app->escape($app['request']->get("id"));
  $location_code = $app->escape($app['request']->get("loc"));
  if(preg_match('/^dedup/', $rec_id)) {
    $record_data = $app['primo_client']->getID($rec_id);
  } else {
    if(preg_match('/^\d+/', $rec_id)) {
      $requested_id = $app['primo_server_connection']['default_pnx_source_id'].$rec_id;
    } else {
      $requested_id = $rec_id;
    }
    $query = new PrimoQuery($requested_id, "any", "exact", $app['primo_server_connection']['default_scope']);
    $record_data = $app['primo_client']->doSearch($query);    

  }
  $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
   
  foreach($primo_record->getHoldings() as $holding) { //iterate through holdings objects 
    if($holding->location_code == $location_code) {
      if($holding->source_id == $requested_id) {
      	$holding_to_map = $holding;
	break; 
      } else {
	$holding_to_map = $holding;
      } 
    }
  }
  
  if(!(isset($holding_to_map))) {
    $app['monolog']->err("TYPE:No Holdings Available for Requested Record ID\tREC_ID:$rec_id\tLOCATION:$location_code"); 
    return "No Holdings at the requested location for Record";
  } else {
      
    if(in_array($holding_to_map->primo_library, $app['stackmap.eligible.libraries'])) {
      /*
       * get the location display Name from locations service because stack map wants it that way
       * should be obtained via a database call in furture when apps mere 
       */ 
      $location_info = json_decode(file_get_contents($app['locations.base'] . "?" . http_build_query(array('loc' => $holding_to_map->location_code))), TRUE); //FIXME
      $map_params = array(
        'callno' => $holding_to_map->call_number,
        'location' => $holding_to_map->location_code,
        'library' => strval($location_info[$holding_to_map->location_code]['libraryDisplay']),
      );
      $map_url = $app['stackmap.base'] . "?" . http_build_query($map_params);
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


/*
 * Route to direct queries to Pulfa
 * 
 */

$app->get('/pulfa/{index_type}/{query}', function($index_type, $query) use($app) {
  if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }
  
  $pulfa = new \Pulfa\Pulfa($app['pulfa']['host'], $app['pulfa']['base']);
  $pulfa_response_data = $pulfa->query($app->escape($query), 0, 3);
  $pulfa_response = new PulfaResponse($pulfa_response_data);
  $brief_response = $pulfa_response->getBriefResponse();
  $brief_response['query'] = $app->escape($query);
  
  $app['monolog']->addInfo("Pulfa Query:" . $query . "\tREFERER:" . $referer);
  return new Response(json_encode($brief_response), 200, array('Content-Type' => 'application/json'));
})->assert('index_type', '(title|any|creator)'); 
 
/*
 * Route to direct queries to Summon API
 * 
 */ 

$app->get('/articles/{index_type}/{query}', function($index_type, $query) use($app) {
  //return "Articles Query " . $app->escape($query);
  if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }
  
  $summon_client = new Summon($app['summon.connection']['client.id'], $app['summon.connection']['authcode']);
  $summon_client->limitToHoldings(); // only bring back Prince results

  if($index_type == 'guide') { //FIXME Only Libguides 
    $summon_client->addFilter('ContentType, Research Guide');
    $summon_data = new SummonResponse($summon_client->query($app->escape($query), 1, 3));
    $response_data = array(
      'query' => $app->escape($query),
      'number' => $summon_data->hits,
      'more' => $app['summon.connection']['base.url'] . $summon_data->deep_search_link,
      'records' => $summon_data->getBriefResults(),
    );
  } elseif ($index_type == "spelling") {
    if($summon_client->checkSpelling($app->escape($query), 1, 1)) {
      $suggestion = $summon_client->checkSpelling($app->escape($query), 1, 1);
    }
    if(isset($suggestion)) {
      $response_data = array($suggestion);
    } else {
      $response_data = array();
    }
  } elseif($index_type == "recommendations") {
    $summon_data = new SummonResponse($summon_client->query($app->escape($query), 1, 3));
    $response_data['recommendations'] = $summon_data->getRecommendations();
    $response_data['number'] = count($response_data['recommendations']);
  } else {
    $summon_client->addCommandFilter("addFacetValueFilters(ContentType,Newspaper+Article:t)"); //FIXME this shoudl default to exclude and retain filter to remove newspapers
    $summon_data = new SummonResponse($summon_client->query($app->escape($query), 1, 3)); 
    //print_r($summon_data);
    $summon_full_search_link = new SummonQuery($app->escape($query), array(
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
  
  
  
  $app['monolog']->addInfo("Summon All Query:" . $query . "\tREFERER:" . $referer);
  return new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=600',));
})->assert('index_type', '(any|title|guide|creator|issn|isbn|spelling|recommendations)');



/*
 * These should be rethought based on a close reading of http://www.exlibrisgroup.org/display/PrimoOI/Brief+Search
 * to make the most generic use of "routes" as possible 
 * anything in the PNX "search" section can be a search index
 * indexes available for the "facets" in a PNX record as well.
 * search by various index types issn, isbn, lccn, oclc
 * 
 * Params accepted
 * 
 * scopes
 *  Example: .....?scopes=ENG,MUSIC - search only english and music libraries
 * 
 * format 
 *  Example: /find/title/journal+of+politics?format=journals - get only items with the journals facet back
 */

 
 $app->get('/find/{index_type}/{query}', function($index_type, $query) use($app) {
  
  if($app['request']->server->get('HTTP_REFERER')) {
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }
  if($app['request']->get('scopes')) {
    $scopes = explode(",", $app['request']->get('scopes'));  
  } else {
    $scopes = array("PRN");
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

  $primo_query = new PrimoQuery($app->escape($query), $app->escape($index_type), $operator, $scopes, $app['primo_server_connection']['num.records.brief.display']);
  if(isset($format_facet)) {
    $primo_query->addFacet($format_facet);
  }
  if(isset($subject_facet)) {
    $primo_query->addFacet($subject_facet); 
  }
  $search_results = $app['primo_client']->doSearch($primo_query);
  if($search_results) {
    $response = new PrimoResponse($search_results, $app['primo_server_connection']);
    $deep_link = new SearchDeepLink($app->escape($query), $app->escape($index_type), $operator, $app['primo_server_connection'], 'location', array("OTHERS", "FIRE"), $primo_query->getFacets());
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
  return new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=600',));
})->assert('index_type', '(issn|isbn|lccn|oclc|title|any|lsr05|creator)'); // should this be a list of possible options from the 




return $app;
