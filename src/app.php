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
));
$app['autoloader']->registerNamespace('PrimoServices',__DIR__.'/../classes');

$app->get('/', function() use($app) {
  return 'Primo Lookup App';
});

/*
 * Redirect Route to Primo Deep Link for IDs
 */
$app->match('/{rec_id}', function($rec_id) use($app) {
  $primo_record_link = new \PrimoServices\PermaLink($rec_id);
  return $app->redirect($primo_record_link->getLink());
})->assert('rec_id', '^(PRN_VOYAGER|dedupmrg)\d+');

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
 * Return all PUL locations associated with a given record
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
 * search by various index types issn, isbn, lccn, oclc
 */
$app->get('/{index_type}/{standard_number}', function($index_type, $standard_number) use($app) {
  return "{$index_type} : {$standard_number}";
})->assert('index_type', '(issn|isbn|lccn|oclc)');

$app->get('/search/{limiter}/{query}', function($limiter, $query) use($app) {
  return "{$app->escape($limiter)} : {$app->escape($query)}";
});

$app['debug'] = true;
return $app;
