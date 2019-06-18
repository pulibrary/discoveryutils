<?php
use Pulfa\Pulfa, 
    Pulfa\Response as PulfaResponse;
use Symfony\Component\HttpFoundation\Response;

$app['pulfa'] = array(
  'host' => "https://findingaids.princeton.edu",
  'base' => "/collections.xml",
  'num.records.brief.display' => 3,
);

/*
 * Route to direct queries to Pulfa
 *
 */
$pulfa = $app['controllers_factory'];
$pulfa->get('/{index_type}', function($index_type) use($app) {
  $app['request'] = $app['request_stack']->getCurrentRequest();
  if($app['request']->get('query')) {
    $query = $app['request']->get('query');
  } else {
    return "No Query Supplied";
  }

  if($app['request']->get('number')) {
    $result_size = $app['request']->get('number');
  } else {
    $result_size = $app['pulfa']['num.records.brief.display'];
  }
  if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }

  $pulfa = new \Pulfa\Pulfa($app['pulfa']['host'], $app['pulfa']['base']);
  $pulfa_response_data = $pulfa->query($query, 0, $result_size);
  $pulfa_response = new PulfaResponse($pulfa_response_data, $query);
  $brief_response = $pulfa_response->getBriefResponse();
  $brief_response['query'] = $app->escape($query);

  $app['monolog']->addInfo("Pulfa Query:" . $query . "\tREFERER:" . $referer);
  return new Response(json_encode($brief_response), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
})->assert('index_type', '(title|any|creator)');


return $pulfa;
?>