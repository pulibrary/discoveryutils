<?php
use FAQ\FAQ,
    FAQ\Response as FAQResponse;
use Symfony\Component\HttpFoundation\Response;

$app['faq'] = array(
  'host' => "https://api2.libanswers.com",
  'base' => "/1.0/search",
  'num.records.brief.display' => 3,
);

$faq = $app['controllers_factory'];
$faq->get('/{search_terms}', function (\Silex\Application $app, $search_terms) {
    $query = $app->escape($search_terms);
    $qString = array();
    $app['request'] = $app['request_stack']->getCurrentRequest();
    if($app['request']->get('group_id')) {
      $qString['group_id'] = $app->escape($app['request']->get('group_id'));
    }

    if($app['request']->get('topics')) {
      $qString['topics'] = $app->escape($app['request']->get('topics'));
    }

    if($app['request']->get('sort')) {
      $qString['sort'] = $app->escape($app['request']->get('sort'));
    }

    if($app['request']->get('sort_dir')) {
      $qString['sort_dir'] = $app->escape($app['request']->get('sort_dir'));
    }

    if($app['request']->get('page')) {
      $qString['page'] = $app->escape($app['request']->get('page'));
    }

    if($app['request']->get('callback')) {
      $qString['callback'] = $app->escape($app['request']->get('callback'));
    }

    if($app['request']->get('limit')) {
      $qString['limit'] = $app['request']->get('limit');
    } else {
      $qString['limit'] = $app['faq']['num.records.brief.display'];
    }

    if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
      $referer = $app['request']->server->get('HTTP_REFERER');
    } else {
      $referer = "Direct Query";
    }

    $faq = new \FAQ\FAQ($app['faq']['host'], $app['faq']['base']);
    $faq_response_data = $faq->query($query, 0, $qString);

    $faq_response = new FAQResponse($faq_response_data, $query);

    $response_data = array(
      'query' => $app->escape($query),
      'number' => $faq_response->hits,
      'more' => $faq_response->more_link->getLink($qString, $query),
      'records' => $faq_response->getBriefResponse(),
    );

    $app['monolog']->addInfo("FAQ Query:" . $query . "\tREFERER:" . $referer);

    $response = new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
    $response->headers->set('Access-Control-Allow-Origin', "*");
    $response->headers->set("Access-Control-Allow-Headers","Content-Type");

    return $response;

})->assert('search_terms', '[\s\w+-]+')->method('GET|OPTIONS');

return $faq;
?>