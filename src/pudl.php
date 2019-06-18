<?php
use Pudl\Pudl,
    Pudl\Response as PudlResponse;
use Symfony\Component\HttpFoundation\Response;

$app['pudl'] = array(
  'host' => "http://pudl.princeton.edu",
  'base' => "/pudl/Objects",
  'num.records.brief.display' => 3,
);

$pudl = $app['controllers_factory'];

$pudl->get('/{index_type}', function(\Silex\Application $app, $index_type) {
  $app['request'] = $app['request_stack']->getCurrentRequest();
  if($app['request']->get('query')) {
    $query = $app['request']->get('query');
  } else {
    return "No Query Supplied";
  }

  if($app['request']->get('format')) {
    $format = $app['request']->get('format');
  } else {
    $format = "json";
  }

  if($app['request']->get('number')) {
    $result_size = $app['request']->get('number');
  } else {
    $result_size = $app['pudl']['num.records.brief.display'];
  }
  if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }

  $pudl = new \Pudl\Pudl($app['pudl']['host'], $app['pudl']['base']);
  $pudl_response_data = $pudl->query($query);

  $pudl_response = new PudlResponse($pudl_response_data, $app->escape($query));
  $brief_response = $pudl_response->getBriefResponse();

  $app['monolog']->addInfo("Pudl Query:" . $query . "\tREFERER:" . $referer);
  if($format == "html") {
    return $app['twig']->render('pudlbrief.html.twig', array(
    'environment' => $app['environment']['env'],
    'title' => $app['environment']['title'],
    'query' => $query,
    'more' => $brief_response['more'],
    'number' => $brief_response['number'],
    'records' => $brief_response['records'],
  ));
  } else {
    return new Response(json_encode($brief_response), 200, array(
      'charset' => 'utf-8',
      'Content-Type' => 'application/json',
      'Cache-Control' => 's-maxage=3600, public'
      )
    );
  }
})->assert('index_type', '(any)');

return $pudl;
?>