<?php
use Blacklight\Blacklight as Blacklight,
    Blacklight\Response as BLResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

$app['blacklight.host'] = "https://catalog.princeton.edu";

$pulsearch = $app['controllers_factory'];

$pulsearch->get('/{index}', function(\Silex\Application $app, $index) {
  $app['request'] = $app['request_stack']->getCurrentRequest();
  if($app['request']->get('query')) {
    $query = $app['request']->get('query');
  } else {
    return "No Query Supplied";
  }
  if ($index == 'isbn') {
    $index_type = 'isbn';
  } elseif ($index == 'issn') {
    $index_type = 'issn';
  } elseif ($index == 'title') {
    $index_type= 'left_anchor';
  } else {
    $index_type = 'all_fields';
  }
  $client = new Blacklight($app['blacklight.host'], '');
  $response = $client->query($query, $index_type);
  $blacklight_response = BLResponse::getResponse($response, $app['blacklight.host']);
  $blacklight_response["more"] = $app['blacklight.host'] . "/catalog?" . "search_field=" . $index_type . "&q=" . urlencode($query) . "&utf8=%E2%9C%93";
  return new JSONResponse($blacklight_response, 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
})->assert('index_type', '(any|issn|isbn|title)');

return $pulsearch;
?>