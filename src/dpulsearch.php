<?php
use Blacklight\Blacklight as Blacklight,
    Blacklight\DpulResponse as DpulResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

$dpulsearch = $app['controllers_factory'];
$dpulsearch->get('/{index}', function(\Silex\Application $app, $index) {
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
  $host_url = 'https://dpul.princeton.edu';
  $client = new Blacklight('https://dpul.princeton.edu', 'catalog');
  $response = $client->query($query, $index_type);
  $blacklight_response = DpulResponse::getResponse($response, $host_url);
  $blacklight_response["more"] = $host_url . "/catalog?" . "search_field=" . $index_type . "&q=" . urlencode($query) . "&utf8=%E2%9C%93";
  return new JSONResponse($blacklight_response, 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
 })->assert('index_type', '(any|issn|isbn|title)');

return $dpulsearch;
?>