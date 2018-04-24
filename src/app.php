<?php

use Silex\Application;
use Silex\Provider;

use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\Yaml\Yaml;
use Summon\Summon,
    Summon\Query as SummonQuery,
    Summon\Response as SummonResponse;
use Pulfa\Pulfa,
    Pulfa\Response as PulfaResponse;
use Pudl\Pudl,
    Pudl\Response as PudlResponse;
use Voyager\Voyager;
use Hours\Hours as Hours;
use Hours\Day as Day;
use Utilities\CoreSearchLink;
use FAQ\FAQ,
    FAQ\Response as FAQResponse;
use Guides\Guides,
    Guides\Response as GuidesResponse;
use Blacklight\Blacklight as Blacklight,
    Blacklight\Response as BLResponse,
    Blacklight\Record as MarcRecord,
    Blacklight\SearchLink as BlacklightSearchLink;

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

/*
$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => __DIR__.'/../cache/',
));
*/
/* Define possible search tabs */
$app['search_tabs'] = array(
  array("index" => "location", "label" => "Catalog+ (Primo/Searchit)"),
  array("index" => "summon", "label" => "Articles + (Summon)"),
  array("index" => "course", "label" => "Course Reserves"),
  array("index" => "blended", "label" => "Catalog and Summon"),
  array("index" => "mendel", "label" => "Mendel Library Audio"),
  array("index" => "mscores", "label" => "Mendal Library Scores"),
  array("index" => "mvideo", "label" => "Mendel Library Video")
);

$app['voyager.connection'] = array(
  'base.url' => "https://catalog.princeton.edu",
  'html.base' => "/cgi-bin/Pwebrecon.cgi",
  'vxws.port' => "7014"
);

# summon api key located in summon.yml

$app['summon.connection'] = array(
  'client.id' => 'princeton',
  'base.url' => 'https://princeton.summon.serialssolutions.com/search?',
  'num.records.brief.display' => 5,
  'authcode' => getenv('SUMMON_AUTHCODE'),
);

$app['pulfa'] = array(
  'host' => "https://findingaids.princeton.edu",
  'base' => "/collections.xml",
  'num.records.brief.display' => 3,
);

$app['guides'] = array(
  'host' => "https://lgapi.libapps.com",
  'base' => "/1.1/guides",
  'num.records.brief.display' => 3,
  'site_id' => '77',
  'key' => '79eb11fd3c26374e9785bb06bc3f3961',
  'status' => '1',
  'external_link_base' => 'http://libguides.princeton.edu/srch.php?',
);

$app['faq'] = array(
  'host' => "https://api2.libanswers.com",
  'base' => "/1.0/search",
  'num.records.brief.display' => 3,
);

$app['library.core'] = array(
  'host' => $core_base_path,
  'all.search.path' => "find/all",
  'db.search.path' => "research/databases/search"
);

$app['pudl'] = array(
  'host' => "http://pudl.princeton.edu",
  'base' => "/pudl/Objects",
  'num.records.brief.display' => 3,
);

$app['hours.base'] = $app['environment']['app_base_url'];
$app['hours.locations'] = 'services/voyager/libraries.json';
$app['hours.weekly'] = 'services/voyager/hours.json';
$app['hours.daily'] = 'hours';
$app['blacklight.host'] = "https://catalog.princeton.edu";
$app['bibdata.host'] = "https://bibdata.princeton.edu";

$app['primo_client'] = function ($app) {
    return new Primo(
    );
};

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


$app->get('/', function() use($app) {

   return $app['twig']->render('home.html.twig', array(
    'environment' => $app['environment']['env'],
    'title' => $app['environment']['title']
  ));
});

$app->get('/hours', function() use($app) {
  $hours_client = new Hours($app['hours.base'], $app['hours.locations'], $app['hours.weekly']);
  $xml = $app['twig']->render('locations.xml.twig', array(
      'libraries' => $hours_client->getCurrentHoursByLocation(),
      'base_url' => $app['environment']['app_base_url'],
      'cur_month' => $hours_client->getCurrentMonth(),
  ));
  return new Response($xml, 200, array('Content-Type'=> 'application/xml'));
});

$app->get('/hours/rbsc', function() use($app) {
  $day_client = new Day($app['hours.base'], $app['hours.daily'] );
  $daily_hours = $day_client->getDailyHoursByLocation();
  return new Response(json_encode($daily_hours), 200, array('Content-Type' => 'application/json'));
});

/*
 * Forms to route data to library core search securely
 */
$app->get('/libraryforms', function() use($app) {
   return $app['twig']->render('forms.html.twig', array(
        'environment' => $app['environment']['env'],
        'title' => "Sample Forms for Library Core System",
        'host' => $app['library.core']['host'],
        'path' => $app['environment']['app_path'],
        'allsearch' => $app['library.core']['all.search.path'],
        'dbsearch'=> $app['library.core']['db.search.path'],
       )
    );
});


/*
 * redirect route for primo basic searches
 * tab should match an available primo search tab
 */
$app->match('/search/{tab}', function(Request $request, $tab) use($app) {
  $app['request'] = $app['request_stack']->getCurrentRequest();
  $query = $app['request']->get('query'); //FIXME escaping this causes primo search to fail
  if($app['request']->server->get('HTTP_REFERER')) {
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }

  if ($tab == "summon") {
    $deep_search_link = new SummonQuery($query, array(
      "s.cmd" => "addFacetValueFilters(ContentType,Newspaper+Article:t)",
      "keep_r" => "true" )
    );
  } elseif($tab == "mendel") {
    $deep_search_link = new BlackLightSearchLink($app['blacklight.host'], $app->escape($query),
                                           array('format' => 'Audio', 'location' => 'Mendel Music Library'));
  } elseif($tab == "mscores") {
    $deep_search_link = new BlacklightSearchLink($app['blacklight.host'], $app->escape($query),
                                           array('format' => 'Musical+score'));

  } elseif($tab == "mvideo") {
    $deep_search_link = new BlacklightSearchLink($app['blacklight.host'], $app->escape($query),
                                           array('format' => 'Video%2FProjected+medium'));
  } elseif($tab == "coreall") {
    $deep_search_link = new CoreSearchLink($app['library.core']['host'], $app['library.core']['all.search.path'] , $app->escape($query));
  } elseif($tab == 'dball') {
    $deep_search_link = new CoreSearchLink($app['library.core']['host'] , $app['library.core']['db.search.path'] , $app->escape($query));
  } else {
    $deep_search_link = new BlacklightSearchLink($app['blacklight.host'], urlencode($query));
  }
  $app['monolog']->addInfo("TAB:" . $tab . "\tQUERY:" . $query . "\tREDIRECT:" . $deep_search_link->getLink() . "\tREFERER:" . $referer);

  return $app->redirect($deep_search_link->getLink());
});

$app->match('/record/{rec_id}.xml', function($rec_id) use($app) {

  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  if(preg_match('/MESSAGE=\"Unauthorized access\"/', $record_data)) {
    return new Response("Unauthorized Access", 403, array('Content-Type' => 'text/plain'));
  } else {
    return new Response($record_data, 200, array('Content-Type' => 'application/xml',
                                                 'Access-Control-Allow-Origin' => "*",
                                                 'Access-Control-Allow-Headers' => "EXLRequestType"));
  }
})->assert('rec_id', '(\w+|EAD\w+\.?\w+)')->method('GET|OPTIONS');

$app->match('/record/{rec_id}.json', function($rec_id) use($app) {

  $record_data = $app['primo_client']->getID($app->escape($rec_id), "true");
  if(preg_match('/MESSAGE=\"Unauthorized access\"/', $record_data)) {
    return new Response("Unauthorized Access", 403, array('Content-Type' => 'text/plain'));
  } else {
    return new Response($record_data, 200, array('Content-Type' => 'application/json',
                                                 'Access-Control-Allow-Origin' => "*",
                                                 'Access-Control-Allow-Headers' => "EXLRequestType"));
  }
})->assert('rec_id', '(\w+|EAD\w+\.?\w+)')->method('GET|OPTIONS');

/*
 * Route to return voyager holdings via html screen scraping
 */

$app->get('/voyager/holdings/{rec_id}', function ($rec_id) use ($app) {
    $voyager_client = new \Voyager\Voyager($app['voyager.connection']);
    $doc_body = $voyager_client->getHoldings($app->escape($rec_id));
    return new Response($doc_body, 200, array('Content-Type' => 'text/html'));
})->assert('rec_id', '\d+');

/*
 * Return On order status & associated messages via JSON
 */

$app->get('/voyager/order/{rec_id}.json', function ($rec_id) use ($app) {
    $voyager_client = new \Voyager\Voyager($app['voyager.connection']);
    $doc_body = $voyager_client->getHoldings($app->escape($rec_id));
    $voyager_record = new \Voyager\Record($doc_body);
    $on_order_response = array();
    $on_order_response['on_order'] = $voyager_record->isOnOrder();
    $on_order_response['order_messages'] = $voyager_record->getOnOrderMessage();

    return new JsonResponse($on_order_response);
})->assert('rec_id', '\d+');

/*
 * Route to direct queries to Pulfa
 *
 */

$app->get('/pulfa/{index_type}', function($index_type) use($app) {
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

/*
 * Route to direct queries to LibGuides
 *
 */

$app->get('/guides/{index_type}', function($index_type) use($app) {
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


/*
 * Route to direct queries to LibAnswers
 *
 */

 $app->get('/faq/{search_terms}', function ($search_terms) use ($app) {

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

 /*
  * Route to direct queries to Digital Library (PUDL)
  *
  */

$app->get('/pudl/{index_type}', function($index_type) use($app) {
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

/*
 * Route to direct queries to Summon API
 *
 */

$app->get('/articles/{index_type}', function($index_type) use($app) {
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

/*
 * Query Blacklight Index
 *
 *
 */

 $app->get('/pulsearch/{index}', function($index) use ($app) {
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
  $client = new Blacklight($app['blacklight.host'], 'catalog.princeton.edu');
  $response = $client->query($query, $index_type);
  $blacklight_response = BLResponse::getResponse($response);
  $blacklight_response["more"] = $app['blacklight.host'] . "/catalog?" . "search_field=" . $index_type . "&q=" . urlencode($query) . "&utf8=%E2%9C%93";
  return new JSONResponse($blacklight_response, 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
 })->assert('index_type', '(any|issn|isbn|title)');

return $app;
