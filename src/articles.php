<?php
use Summon\Summon,
    Summon\Query as SummonQuery,
    Summon\Response as SummonResponse;
use Symfony\Component\HttpFoundation\Response;

$app['summon.connection'] = array(
  'client.id' => 'princeton',
  'base.url' => 'https://princeton.summon.serialssolutions.com/search?',
  'num.records.brief.display' => 5,
  'authcode' => getenv('SUMMON_AUTHCODE'),
);


$articles = $app['controllers_factory'];
$articles->get('/{index_type}', function(\Silex\Application $app, $index_type) {
  $app['request'] = $app['request_stack']->getCurrentRequest();
  if($app['request']->get('query')) {
    $query = $app['request']->get('query');
  } else {
    return "No Query Supplied";
  }

  if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }

  if($app['request']->get('number')) {
    $result_size = $app['request']->get('number');
  } else {
    $result_size = $app['summon.connection']['num.records.brief.display'];
  }


  $summon_client = new Summon($app['summon.connection']['client.id'], $app['summon.connection']['authcode']);
  $summon_client->limitToHoldings(); // only bring back Princeton results

  if($index_type == 'guide') {
    $summon_client->addFilter('ContentType, Research Guide');
    $summon_data = new SummonResponse($summon_client->query($query, 1, 3));
    $summon_full_search_link = new SummonQuery($query, array(
      "s.fvf" => 'ContentType,Research Guide',
      "keep_r" => "true",
      "s.dym" => "t",
      "s.ho" => "t"
    ));
    $response_data = array(
      'query' => $app->escape($query),
      'number' => $summon_data->hits,
      'more' => $summon_full_search_link->getLink(),
      'records' => $summon_data->getBriefResults(),
    );
  } elseif ($index_type == "spelling") {
    if($summon_client->checkSpelling($query, 1, 1)) {
      $suggestion = $summon_client->checkSpelling($query, 1, 1);
    }
    if(isset($suggestion)) {
      $response_data = array($suggestion);
    } else {
      $response_data = array();
    }
  } elseif($index_type == "recommendations") {
    $summon_data = new SummonResponse($summon_client->query($query, 1, 3));
    $response_data['recommendations'] = $summon_data->getRecommendations();
    $response_data['number'] = count($response_data['recommendations']);
  } else {
    $summon_client->addCommandFilter("addFacetValueFilters(ContentType,Newspaper+Article:t,Book+Review:t)"); //FIXME this shoudl default to exclude and retain filter to remove newspapers
    $summon_client->addFilter("IsScholarly,true");
    $summon_data = new SummonResponse($summon_client->query($query, 1, $result_size));
    //print_r($summon_data);
    $summon_full_search_link = new SummonQuery($query, array(
      //"s.cmd" => "addFacetValueFilters(ContentType,Newspaper+Article:t,Book+Review:t)",
      "s.fvf" => "IsScholarly,true",
      "keep_r" => "true",
      "s.dym" => "t",
      "s.ho" => "t"
      )
    );
    $response_data = array(
      'query' => $app->escape($query),
      'number' => $summon_data->hits,
      'more' => $summon_full_search_link->getLink(),
      'records' => $summon_data->getBriefResults(),
    );
  }

  $app['monolog']->addInfo("Summon $index_type Query:" . $query . "\tREFERER:" . $referer);
  return new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
})->assert('index_type', '(any|title|guide|creator|issn|isbn|spelling|recommendations)');

return $articles;
?>