<?php

require_once __DIR__.'/../vendor/silex.phar';
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use PrimoServices\PrimoRecord;
use PrimoServices\PrimoClient;
use PrimoServices\PrimoLoader;

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

$app->get('/hello/{name}', function ($name) use ($app) {
  $app['monolog']->addInfo(sprintf("User '%s' dropped by to say hi.", $name));
  return $app['twig']->render('hello.twig', array(
    'name' => $name,
  ));
}); 

$app->get('/record/{rec_id}', function($rec_id) use($app) {
  $primo_client = new \PrimoServices\PrimoClient();
  $record_data = $primo_client->getID($app->escape($rec_id));
  $primo_record = new \PrimoServices\PrimoRecord($record_data);
  $stub_data = $primo_record->getBriefInfo();
  return new Response(json_encode($stub_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+'); //test regular expression validation of route 

$app->get('/{index_type}/{standard_number}', function($index_type, $standard_number) use($app) {
  return "{$index_type} : {$standard_number}";
});

$app->get('/search/{limiter}/{query}', function($limiter, $query) use($app) {
  return "{$app->escape($limiter)} : {$app->escape($query)}";
});

$app['debug'] = true;
return $app;
