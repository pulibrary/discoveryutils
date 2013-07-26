<?php
require_once __DIR__.'/vendor/autoload.php';
$app = require dirname(__FILE__).'/src/app.php';

/* set the app up */

if($app['debug']) {
  $app->run();
} else {
  //if deployed behind varnish no need for Silex file system cache
  //$app['http_cache']->run(); 
  $app->run();
}
