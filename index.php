<?php
require_once __DIR__.'/vendor/autoload.php';
$app = require dirname(__FILE__).'/src/app.php';

/* set the app up */
$app['debug'] = true;
if($app['debug']) {
  $app->run();
} else {
  $app['http_cache']->run(); 
}
