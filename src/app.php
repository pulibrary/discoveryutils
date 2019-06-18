<?php

use Silex\Application;
use Silex\Provider;

$app = new Silex\Application();

$app['environment'] = array(
  'env' => 'development',
  'title' => 'Princeton Library Discovery Service',
  'app_base_url' => getenv('DISCOVERYUTILS_BASE_URL'),
  'app_path' => 'utils'
);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path'       => __DIR__.'/../views',
));

$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\RoutingServiceProvider());
#$app->register(new Provider\UrlGeneratorServiceProvider());

if ($app['environment']['env'] == 'development') {
  $log_level = 'DEBUG';
} else {
  $log_level = 'INFO';
}

$core_base_path = $app['environment']['app_base_url'];

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile'       => __DIR__.'/../log/usage.log',
    'monolog.level'         => $log_level
));

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

$app->mount('/', include 'home.php');

$app->mount('/hours', include 'hours.php');

$app->mount('/hours/rbsc', include 'hours_rbsc.php');

/*
 * Forms to route data to library core search securely
 */
$app->mount('/libraryforms', include 'library_forms.php');

/*
 * Route to direct queries to Pulfa
 *
 */
$app->mount('/pulfa', include 'pulfa.php');

/*
 * Route to direct queries to LibGuides
 *
 */
$app->mount('/guides', include 'guides.php');

/*
 * Route to direct queries to LibAnswers
 *
 */
$app->mount('/faq', include 'faq.php');

/*
  * Route to direct queries to Digital Library (PUDL)
  *
  */
$app->mount('/pudl', include 'pudl.php');

/*
 * Route to direct queries to Summon API
 *
 */
$app->mount('/articles', include 'articles.php');

/*
 * Query Blacklight Index
 *
 */
$app->mount('/pulsearch', include 'pulsearch.php');


$app->mount('/dpulsearch', include 'dpulsearch.php');

/*
 * Query Blacklight Map Index
 *
 */
$app->mount('/mapsearch', include 'mapsearch.php');

return $app;
