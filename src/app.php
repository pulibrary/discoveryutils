<?php

require_once __DIR__.'/../vendor/silex.phar';
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use PrimoServices\PrimoRecord;
use PrimoServices\PrimoClient;
use PrimoServices\PrimoLoader;
use PrimoServices\PermaLink;

/* bootstrap */

$app = new Silex\Application(); 

$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path'       => __DIR__.'/../views',
  'twig.class_path' => __DIR__.'/../vendor/Twig/lib',
));
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile'       => __DIR__.'/../log/development.log',
    'monolog.class_path'    => __DIR__.'/../vendor/Monolog/src',
    'monolog.level'         => 'Logger::DEBUG'
));

/*
$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array(
    'swiftmailer.class_path'  => __DIR__.'/../vendor/swiftmailer/lib/classes',
));
*/
$app['autoloader']->registerNamespace('PrimoServices',__DIR__.'/../classes');

$app->get('/', function() use($app) {
  return 'Primo Lookup App';
});

/*
 * Redirect Route to Primo Deep Link for IDs
 */
$app->match('/show/{rec_id}', function($rec_id) use($app) {
  $primo_record_link = new \PrimoServices\PermaLink($rec_id);
  $app['monolog']->addInfo("REDIRECT: " . $primo_record_link->getLink());
  return $app->redirect($primo_record_link->getLink());
})->assert('rec_id', '^(PRN_VOYAGER|dedupmrg)\d+');

/* 
 * redirect route for primo basic searches 
 * tab should match an available primo search tab
 */
$app->match('/search/{tab}', function($tab) use($app) {
  //test to see if query is valid
  $query = $app->escape($app['request']->get('query')); //protect query against XSS
  
  if ($tab == "summon") {
    $deep_search_link = new \PrimoServices\SummonQuery($query);
  } elseif($tab == "course") {
    $deep_search_link = new \PrimoServices\SearchDeepLink($query, "any", "contains", $tab, array());
  } elseif($tab == "blended") {
    $deep_search_link = new \PrimoServices\SearchDeepLink($query, "any", "contains", $tab, array("PRN", "SummonThirdNode"));
  } else {
    $deep_search_link = new \PrimoServices\SearchDeepLink($query, "any", "contains", $tab, array("PRN"));
  }
  $app['monolog']->addInfo("TAB:" . $tab . "\tREDIRECT: " . $deep_search_link->getLink());
  return $app->redirect($deep_search_link->getLink());
});


/* 
 *  Test Route
 */
$app->get('/hello/{name}', function ($name) use ($app) {
  $app['monolog']->addInfo(sprintf("User '%s' dropped by to say hi.", $name));
  return $app['twig']->render('hello.twig', array(
    'name' => $name,
  ));
}); 

$app->get('/record/{rec_id}.json', function($rec_id) use($app) {
  $primo_client = new \PrimoServices\PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  $primo_record = new \PrimoServices\PrimoRecord($record_data);
  $stub_data = $primo_record->getBriefInfo();
  $app['monolog']->addInfo("PNXID_REQUEST: " . json_encode($stub_data));
  return new Response(json_encode($stub_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+'); //test regular expression validation of route 

$app->get('/record/{rec_id}.xml', function($rec_id) use($app) {
  $primo_client = new \PrimoServices\PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  return new Response($record_data, 200, array('Content-Type' => 'application/xml'));
})->assert('rec_id', '\w+'); 

$app->get('/record/{rec_id}', function($rec_id) use($app) {
  $primo_client = new \PrimoServices\PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  $primo_record = new \PrimoServices\PrimoRecord($record_data);
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
  $primo_client = new \PrimoServices\PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  $primo_record = new \PrimoServices\PrimoRecord($record_data);
  $all_links_data = $primo_record->getAllLinks();
  return new Response(json_encode($all_links_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

/*
 * Return all PUL locations associated with a given primo id
 */
$app->get('/locations/{rec_id}.json', function($rec_id) use($app) {
  $primo_client = new \PrimoServices\PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  $primo_record = new \PrimoServices\PrimoRecord($record_data);
  $all_links_data = $primo_record->getAvailableLibraries();
  if ($view_type = $app['request']->get('view')) { // this method may be slow per symfony request class docs
    $all_links_data['view'] =  $view_type; 
  }
  return new Response(json_encode($all_links_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

/*
 * Generic "services" route to all for querying of specific primo services
 * Returns values for {locations, openurl, fulltext, delivery, borrowdirect }
 */
$app->get('/{rec_id}/{service_type}.{format}', function($rec_id, $service_type, $format="html") use ($app) {
  $primo_client = new \PrimoServices\PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id)); //FIXME perhaps try and use the symfony validator utility to filter all rec_ids and service_types
  $primo_record = new \PrimoServices\PrimoRecord($record_data);
  // decide which service type to use
  $location_links_data = $primo_record->getLocationServices();
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
$app->get('/{index_type}/{standard_number}', function($index_type, $standard_number) use($app) {
  $primo_client = new \PrimoServices\PrimoClient();
  $query = new \PrimoServices\PrimoQuery($app->escape($standard_number), $app->escape($index_type));
  $response_data = $primo_client->doSearch($query);
  //$record_data = new \PrimoServices\PrimoRecord($response_data);
  //echo $primo_client;
  $app['monolog']->addInfo("Index Query: " . $primo_client);
  //return $app->redirect($primo_client);
  return new Response($response_data, 200, array('Content-Type' => 'application/xml'));
  //echo $query->getQueryString();
})->assert('index_type', '(issn|isbn|lccn|oclc)'); // should this be a list of possible options from the 

/*
 * For general keyword/title searches 
 */
$app->get('/search/{limiter}/{query}', function($index_type, $query) use($app) {
  $app['monolog']->addInfo("{$app->escape($limiter)} : {$app->escape($query)}");
  $primo_client = new \PrimoServices\PrimoClient();
  $query = new \PrimoServices\PrimoQuery($app->escape($query), $app->escape($index_type));
  
})->assert('index_type', '\w+'); // should be a list of values that are valid for the indexField parameter in a Primo query statement

$app['debug'] = true;
return $app;
