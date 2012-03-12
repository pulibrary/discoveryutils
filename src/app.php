<?php

require_once __DIR__.'/../vendor/silex.phar';
use Symfony\Component\HttpFoundation\Response,
  Symfony\Component\HttpFoundation\Request;
use PrimoServices\PrimoRecord,
  PrimoServices\PrimoClient,
  PrimoServices\PrimoLoader,
  PrimoServices\PermaLink,
  PrimoServices\PrimoException,
  PrimoServices\SummonQuery,
  PrimoServices\PrimoQuery,
  PrimoServices\RequestClient,
  PrimoServices\SearchDeepLink;

$app = new Silex\Application(); 

$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path'       => __DIR__.'/../views',
  'twig.class_path' => __DIR__.'/../vendor/Twig/lib',
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile'       => __DIR__.'/../log/usage.log',
    'monolog.class_path'    => __DIR__.'/../vendor/Monolog/src',
    'monolog.level'         => 'Logger::DEBUG'
));

$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => __DIR__.'/../cache/',
));


/*
$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array(
    'swiftmailer.class_path'  => __DIR__.'/../vendor/swiftmailer/lib/classes',
));
 */

$app['autoloader']->registerNamespaces(array(
  'PrimoServices' => __DIR__.'/../classes',
));

//print_r($app['autoloader']->getNamespaces());
$app->get('/', function() use($app) {
  return 'Primo Lookup App';
});

/*
 * Redirect Route to Primo Deep Link for IDs
 */
$app->match('/show/{rec_id}', function($rec_id) use($app) {
  $primo_record_link = new PermaLink($rec_id);
  $app['monolog']->addInfo("REDIRECT: " . $primo_record_link->getLink());
  return $app->redirect($primo_record_link->getLink());
})->assert('rec_id', '^(PRN_VOYAGER|dedupmrg)\d+');

/* 
 * redirect route for primo basic searches 
 * tab should match an available primo search tab
 */
$app->match('/search/{tab}', function($tab) use($app) {
  //test to see if query is valid
  $query = $app['request']->get('query'); //FIXME escaping this causes primo search to fail 
  
  if ($tab == "summon") {
    $deep_search_link = new SummonQuery($query);
  } elseif($tab == "course") {
    $deep_search_link = new SearchDeepLink($query, "any", "contains", $tab, array("COURSE"));
  } elseif($tab == "blended") {
    $deep_search_link = new SearchDeepLink($query, "any", "contains", $tab, array("PRN", "SummonThirdNode"));
  } else {
    $deep_search_link = new SearchDeepLink($query, "any", "contains", $tab, array("OTHERS", "FIRE")); //WATCHOUT - Order Matters 
  }
  $app['monolog']->addInfo("TAB:" . $tab . "\tQUERY:" . $query . "\tREDIRECT: " . $deep_search_link->getLink());
  return $app->redirect($deep_search_link->getLink());
  //return $deep_search_link->getLink();
});


/* 
 *  Test Route
 */
$app->get('/hello/{name}', function ($name) use ($app) {
  $app['monolog']->addInfo(sprintf("User '%s' dropped by to say hi.", $name));
  $content = $app['twig']->render('hello.twig', array(
    'name' => $name,
  ));
  return new Response($content, 200, array(
    'Cache-Control' => 'public, s-maxage=3600',
    //'Surrogate-Control' => 'content="ESI/1.0"',
  ));
}); 

$app->get('/record/{rec_id}.json', function($rec_id) use($app) {
  $primo_client = new PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data);
  $stub_data = $primo_record->getBriefInfo();
  $app['monolog']->addInfo("PNXID_REQUEST: " . json_encode($stub_data));
  return new Response(json_encode($stub_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+'); //test regular expression validation of route 

$app->get('/record/{rec_id}.xml', function($rec_id) use($app) {
  $primo_client = new PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  return new Response($record_data, 200, array('Content-Type' => 'application/xml'));
})->assert('rec_id', '\w+'); 

$app->get('/record/{rec_id}.ris', function($rec_id) use($app) {
  $primo_client = new PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data);
  $ris_data = $primo_record->getCitation("RIS");
  $app['monolog']->addInfo("RIS_REQUEST: " . $rec_id . "\n" . $ris_data);
  return new Response($ris_data, 200, array('Content-Type' => 'application/x-research-info-systems'));
})->assert('rec_id', '\w+');


$app->get('/record/{rec_id}', function($rec_id) use($app) {
  $primo_client = new PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data);
  $stub_data = $primo_record->getBriefInfo();
  $response_data = array();
  $response_data['rec_id'] = $rec_id;
  $response_data['pnx_response'] = $stub_data;
  //$stub_data['source_prn_id'] = $rec_id;
  return $app['twig']->render('record.twig', $response_data);
})->assert('rec_id', '\w+');

/*
 * return all links associated with a given primo id
 */
$app->get('/links/{rec_id}.json', function($rec_id) use($app) {
  $primo_client = new PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data);
  $all_links_data = $primo_record->getAllLinks();
  return new Response(json_encode($all_links_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

/*
 * Return all PUL locations associated with a given primo id
 */
$app->get('/locations/{rec_id}.json', function($rec_id) use($app) {
  $primo_client = new PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data);
  $all_links_data = $primo_record->getAvailableLibraries();
  if ($view_type = $app['request']->get('view')) { // this method may be slow per symfony request class docs
    $all_links_data['view'] =  $view_type; 
  }
  return new Response(json_encode($all_links_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

$app->get('/availability/{rec_id}.json', function($rec_id) use($app) {
  $availability_client = new RequestClient($app->escape($rec_id));
  $availability_response = $availability_client->doLookup();
  //$decoded_reponse = json_decode($availability_response);
  $app['monolog']->addInfo("Request Lookup: " . $availability_client);
  return new Response($availability_response, 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

$app->get('/availability/{rec_id}', function($rec_id) use($app) {
  $availability_client = new RequestClient($app->escape($rec_id));
  $availability_response = $availability_client->doLookup();
  //$decoded_reponse = json_decode($availability_response);
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
  $primo_client = new PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id)); //FIXME perhaps try and use the symfony validator utility to filter all rec_ids and service_types
  $primo_record = new PrimoRecord($record_data);
  // decide which service type to use
  $location_links_data = $primo_record->getAllLinks();
  if ($format == "json") {
    return new Response(json_encode($location_links_data), 200, array('Content-Type' => 'application/json'));
  }
  // decide which format to return
})->assert('rec_id', '\w+');

/*
 * These should be rethought based on a close reading of http://www.exlibrisgroup.org/display/PrimoOI/Brief+Search
 * to make the most generic use of "routes" as possible 
 * anything in the PNX "search" section can be a search index
 * indexes available for the "facets" in a PNX record as well.
 * search by various index types issn, isbn, lccn, oclc
 */
$app->get('/find/{index_type}/{query}', function($index_type, $query) use($app) {
  
  if($app['request']->get('scopes')) {
    $scopes = explode(",", $app['request']->get('scopes'));  
  } else {
    $scopes = array("PRN");
  }
  if($app['request']->get('limit')) {
    $operator = $app->escape($app['request']->get('limit'));
  } else {
    $operator = "exact";
  }
  $primo_client = new PrimoClient();
  $query = new PrimoQuery($app->escape($query), $app->escape($index_type), $operator, $scopes);
  $response_data = $primo_client->doSearch($query);
  $app['monolog']->addInfo("Index Query: " . $primo_client);
  
  return new Response($response_data, 200, array('Content-Type' => 'application/xml'));
})->assert('index_type', '(issn|isbn|lccn|oclc|title|any)'); // should this be a list of possible options from the 

// should kick only on prod
//$app['http_cache']->run(); 

return $app;
