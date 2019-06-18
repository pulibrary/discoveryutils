<?php
use Guides\Guides,
    Guides\Response as GuidesResponse;
use Symfony\Component\HttpFoundation\Response;

$app['guides'] = array(
  'host' => "https://lgapi.libapps.com",
  'base' => "/1.1/guides",
  'num.records.brief.display' => 3,
  'site_id' => '77',
  'key' => '79eb11fd3c26374e9785bb06bc3f3961',
  'status' => '1',
  'external_link_base' => 'http://libguides.princeton.edu/srch.php?',
);

$guide = $app['controllers_factory'];
$guide->get('/{index_type}', function(\Silex\Application $app, $index_type) {
  $app['request'] = $app['request_stack']->getCurrentRequest();
  $qString = array();

  if($app['request']->get('query')) {
    $raw_query = $app['request']->get('query');
    $query = $app->escape($raw_query);
  } else {
    return "No Query Supplied";
  }

  if($app['request']->get('number')) {
    $result_size = $app['request']->get('number');
  } else {
    $result_size = $app['guides']['num.records.brief.display'];
  }
  if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }

  $guides = new \Guides\Guides($app['guides']);
  $guides_response_data = $guides->query($query, 0, $qString);
  $guides_response = new GuidesResponse($guides_response_data, $query);

  $response_data = array(
       'query' => $guides_response->query,
       'number' => count($guides_response->getBriefResponse()),
       'more' => $guides_response->more_link,
       'records' => $guides_response->getBriefResponse(),
     );

  $app['monolog']->addInfo("Guides Query:" . $query . "\tREFERER:" . $referer);

  return new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
})->assert('index_type', '(any|title)');

return $guide;
?>