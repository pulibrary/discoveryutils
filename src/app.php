<?php

use Silex\Application;
use Silex\Provider;

use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\Yaml\Yaml;
use Primo\Record as PrimoRecord,
    Primo\PermaLink as Permalink,
    Primo\Query as PrimoQuery,
    Primo\Primo as Primo,
    Primo\SearchDeepLink as SearchDeepLink,
    Primo\Response as PrimoResponse,
    Primo\ScopeList as PrimoScopeList;
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

$app['environment'] = Yaml::parse(__DIR__.'/../conf/environment.yml');
$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path'       => __DIR__.'/../views',
));

$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\UrlGeneratorServiceProvider());

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

$app['primo_server_connection'] = Yaml::parse(__DIR__.'/../conf/primo.yml');

$app['voyager.connection'] = array(
  'base.url' => "http://catalog.princeton.edu",
  'html.base' => "/cgi-bin/Pwebrecon.cgi",
  'vxws.port' => "7014"
);

# summon api key located in summon.yml

$app['summon.connection'] = Yaml::parse(__DIR__.'/../conf/summon.yml');

$app['pulfa'] = array(
  'host' => "http://findingaids.princeton.edu",
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

$app['locator.base'] = "http://library.princeton.edu/locator/index.php";
// get primo scopes via webservices http://searchit.princeton.edu/PrimoWebServices/xservice/getscopesofview?viewId=PRINCETON
$app['stackmap'] = Yaml::parse(__DIR__.'/../conf/stackmap.yml');

$app['locations.base'] = "http://library.princeton.edu/requests/locationservice.php";
$app['locations.list'] = json_decode(__DIR__.'/../conf/locations.json');

$app['hours.base'] = $app['environment']['app_base_url'];
$app['hours.locations'] = 'services/voyager/libraries.json';
$app['hours.weekly'] = 'services/voyager/hours.json';
$app['hours.daily'] = 'hours';
$app['blacklight.host'] = "https://pulsearch.princeton.edu";
$app['bibdata.host'] = "https://bibdata.princeton.edu";

$app['primo_client'] = $app->share(function ($app) {
    return new Primo($app['primo_server_connection']);
});

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

$app->get('/hours/dow', function() use($app) {
  $hours_client = new Hours($app['hours.base'], $app['hours.locations'], $app['hours.weekly']);
  $hours_client->getDowHours();
  $xml = $app['twig']->render('dow.xml.twig', array(
      'libraries' => $hours_client->getCurrentHoursByLocation(),
      'base_url' => $app['environment']['app_base_url'],
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
 * Redirect Route to Primo Deep Link for IDs
 */
$app->match('/show/{rec_id}', function($rec_id) use($app) {

  $primo_record_link = new PermaLink($rec_id, $app['primo_server_connection']);
  $app['monolog']->addInfo("REDIRECT: " . $primo_record_link->getLink());
  return $app->redirect($primo_record_link->getLink());
})->assert('rec_id', '^(PRN_VOYAGER|dedupmrg)\d+');

/*
 * redirect route for primo basic searches
 * tab should match an available primo search tab
 */
$app->match('/search/{tab}', function(Request $request, $tab) use($app) {

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
    $deep_search_link = new BlacklightSearchLink($app['blacklight.host'], $app->escape($query));
  }
  $app['monolog']->addInfo("TAB:" . $tab . "\tQUERY:" . $query . "\tREDIRECT:" . $deep_search_link->getLink() . "\tREFERER:" . $referer);

  return $app->redirect($deep_search_link->getLink());
});

$app->get('/briefpnx/{rec_id}.json', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  if(preg_match('/MESSAGE=\"Unauthorized access\"/', $record_data)) {
    return new Response("Unauthorized Access", 403, array('Content-Type' => 'text/plain'));
  } else {
    $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
    $stub_data = $primo_record->getBriefInfo();
    $app['monolog']->addInfo("PNXID_REQUEST: " . $rec_id);
    return new Response(json_encode($stub_data), 200, array('Content-Type' => 'application/json'));
  }
})->assert('rec_id', '\w+'); //test regular expression validation of route

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

$app->get('/record/{rec_id}.ris', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  if(preg_match('/MESSAGE=\"Unauthorized access\"/', $record_data)) {
    return new Response("Unauthorized Access", 403, array('Content-Type' => 'text/plain'));
  } else {
    $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
    $ris_data = $primo_record->getCitation("RIS");
    $app['monolog']->addInfo("RIS_REQUEST: " . $rec_id . "\n" . $ris_data);
    return new Response($ris_data, 200, array('Content-Type' => 'application/x-research-info-systems'));
  }
})->assert('rec_id', '\w+');


$app->get('/record/{rec_id}', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  if(preg_match('/MESSAGE=\"Unauthorized access\"/', $record_data)) {
      return new Response("Unauthorized Access", 403, array('Content-Type' => 'text/plain'));
  } else {
    $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
    $stub_data = $primo_record->getBriefInfo();
    $response_data = array();
    $response_data['rec_id'] = $rec_id;
    $response_data['pnx_response'] = $stub_data;

    return $app['twig']->render('record.twig', $response_data);
  }
})->assert('rec_id', '\w+');

/*
 * build a map for a given location code and id
 */

/* do not send dedup ids to this controller
 *
 * @params
 * id = Voyager Style Numeric ID (Maybe should accept either one)
 * loc = Voyager Location Code
 *
 * Example http://mydiscservice.edu/map?id=123456&loc=stax
 *
 * */
$app->get('/map', function() use ($app) {
  $referer = "DIRECT";
  if($app['request']->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
    $referer = $app['request']->server->get('HTTP_REFERER');
  }
  $rec_id = $app->escape($app['request']->get("id")); //FIXME Should through an error if neither parameter is present
  $location_code = $app->escape($app['request']->get("loc"));

  if(preg_match('/^dedup/', $rec_id)) {
    $record_data = $app['primo_client']->getID($rec_id);
  } else {
    if(preg_match('/^\d+/', $rec_id)) {
      $requested_id = $app['primo_server_connection']['default_pnx_source_id'].$rec_id;
    } else {
      $requested_id = $rec_id;
    }
    $query = new PrimoQuery($requested_id, "any", "exact", $app['primo_server_connection']['default.scope']);
    $record_data = $app['primo_client']->doSearch($query);

  }
  $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);

  foreach($primo_record->getHoldings() as $holding) {
    if($holding->location_code == $location_code) {
      if($holding->source_id == $requested_id) {
      	$holding_to_map = $holding;
        break; //why break from loop here?
      } else {
        $holding_to_map = $holding;
      }
    }
  }
  /*
   * ***Note on Empty Holdings*******
   *
   * If there is no holding in Primo Web Services should treat the item differently depending on whether or not it came
   * from Voyager or Searchit. There can be discrepancies for temp holdings.
   *
   * For Searchit - generate an error.
   * For Voyager - Just pass through to the PUL Locator for an item not a stackmap eligible library
   *
   */

  if(!(isset($holding_to_map))) {
    // most likely for temp/perm location mismatches
    // send these over to the locator
    $app['monolog']->err("TYPE:No Holdings Available for Requested Record ID\tREC_ID:$rec_id\tLOCATION:$location_code\tREFERER:$referer"); //log the error

    $map_params = array(
        'loc' => $location_code,
        'id' => $rec_id,
      );
    $map_url = $app['locator.base'] . "?" . http_build_query($map_params);

    $app['monolog']->addInfo("MAP:$map_url\tLOCATION:$location_code\tRECORD:$rec_id");

    return $app->redirect($map_url);

  } elseif(in_array($holding_to_map->location_code, $app['stackmap']['reserve.locations'])) {
    $location_info = json_decode(file_get_contents($app['locations.base'] . "?" . http_build_query(array('loc' => $holding_to_map->location_code))), TRUE); //FIXME
    $app['monolog']->addInfo("StackMap reserve MAP:$map_url\tLOCATION:$location_code\tRECORD:$rec_id");
    return $app['twig']->render('reserve.twig', array(
       'record_id' => $rec_id,
       'title' => $primo_record->getNormalizedTitle(),
       'call_number' => $holding_to_map->call_number,
       'library' => $location_info[$holding_to_map->location_code]['libraryDisplay'],
       'location_label' => $location_info[$holding_to_map->location_code]['collectionDisplay']
       ));
  } else {

    if(in_array($holding_to_map->primo_library, $app['stackmap']['eligible.libraries'])) { //FIXE
       /*
       * get the location display Name from locations service because stack map wants it that way
       * should be obtained via a database call in future when apps mere
       */
      $location_info = json_decode(file_get_contents($app['locations.base'] . "?" . http_build_query(array('loc' => $holding_to_map->location_code))), TRUE); //FIXME
      if(in_array($holding_to_map->location_code, $app['stackmap']['by.title.locations'])) {

        $shelf_loc_title = MarcRecord::getTitle($app['bibdata.host'] . "/bibliographic/" . $rec_id);
        $call_number = $shelf_loc_title; //$primo_record->getNormalizedTitle();
        $app['monolog']->addInfo("Stackmap by Title MAP:$map_url\tLOCATION:$location_code\tRECORD:$rec_id");
      } else {
	$holdings_list = $primo_record->getHoldings();
        //$call_number = $primo_record->getCallNumber(); //$holding_to_map->call_number;
	$call_number = "";
	foreach($holdings_list as $holding) {
	  if($location_code == $holding->location_code) {
	    $call_number = trim($holding->call_number);
	    $call_number = ltrim($call_number, "(");
	    $call_number = rtrim($call_number, ")");
	    $call_number = trim($call_number);
	  }
	}
	$app['monolog']->addInfo("Stackmap Standard Location:$map_url:$call_number:LOCATION:$location_code\tRECORD:$rec_id");
      }
      $map_params = array(
        'callno' => $call_number,
        'location' => $holding_to_map->location_code,
        'library' => strval($location_info[$holding_to_map->location_code]['libraryDisplay']),
      );
      $map_url = $app['stackmap']['base.url'] . "?" . http_build_query($map_params);
    } else {

      $map_params = array(
        'loc' => $holding_to_map->location_code,
        'id' => $rec_id,
      );
      $map_url = $app['locator.base'] . "?" . http_build_query($map_params);
    }
    $app['monolog']->addInfo("MAP:$map_url\tLOCATION:$location_code\tRECORD:$rec_id");

    return $app->redirect($map_url);
  }
});



/*
 * return all links associated with a given primo id
 */
$app->get('/links/{rec_id}.json', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
  $all_links_data = $primo_record->getAllLinks();
  return new Response(json_encode($all_links_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

/*
 * Return all PUL locations associated with a given primo id
 */
$app->get('/locations/{rec_id}.json', function($rec_id) use($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id));
  $primo_record = new PrimoRecord($record_data, $app['primo_server_connection']);
  $all_links_data = $primo_record->getAvailableLibraries();
  if ($view_type = $app['request']->get('view')) { // this method may be slow per symfony request class docs
    $all_links_data['view'] =  $view_type;
  }
  return new Response(json_encode($all_links_data), 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

$app->get('/availability/{rec_id}.json', function($rec_id) use($app) {
  $availability_response = $app['primo_client']->getID($app->escape($rec_id));
  $app['monolog']->addInfo("Availability Lookup: " . $app->escape($rec_id));
  return new Response($availability_response, 200, array('Content-Type' => 'application/json'));
})->assert('rec_id', '\w+');

$app->match('/archives/{rec_id}', function($rec_id) use($app) {
  $record_response = $app['primo_client']->getID($app->escape($rec_id));
  $app['monolog']->addInfo("Availability Lookup: " . $app->escape($rec_id));

  $record = new PrimoRecord($record_response, $app['primo_server_connection']);
  $response = New Response($app['twig']->render('archives.html.twig', array(
    'source' => $record->getSourceID(),
    'record_id' => $rec_id,
    'archival_holding' => $record->getArchivalHoldings(),
    'items' => $record->getArchivalItems(),
    'title' => "Reading Room Request: " . $record->getTitle(),
    'doc_title' => $record->getTitle(),
    'environment' => $app['environment']['env'],
  )), 200);
  $response->headers->set('Access-Control-Allow-Origin', "*");
  $response->headers->set('Access-Control-Allow-Headers', "EXLRequestType");
  return $response;
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
 * Generic "services" route to all for querying of specific primo services
 * Returns values for {locations, openurl, fulltext, delivery, borrowdirect }
 */
$app->get('/{rec_id}/{service_type}.{format}', function($rec_id, $service_type, $format="html") use ($app) {
  $record_data = $app['primo_client']->getID($app->escape($rec_id)); //FIXME perhaps try and use the symfony validator utility to filter all rec_ids and service_types
  $primo_record = new PrimoRecord($record_data,$app['primo_server_connection']);
  // decide which service type to use
  $location_links_data = $primo_record->getAllLinks();

  if ($format == "json") {
    return new Response(json_encode($location_links_data), 200, array('Content-Type' => 'application/json'));
  }
})->assert('rec_id', '\w+');

$app->post('/scopelist', function() use ($app) {
  $scope_list_response = $app['primo_client']->getScopes();
  $scope_list = new PrimoScopeList($scope_list_response);
  $yaml = $scope_list->asYaml();
  //updat yaml file
  file_put_contents(__DIR__.'/../conf/scopes.yml', $yaml);

  return new JsonResponse($scope_list->getScopes());
});

$app->post('/locations', function() use ($app) {

  $locations = json_decode(file_get_contents($app['locations.base']), TRUE);
  ksort($locations);
  $location_codes = array();
  foreach($locations as $loc_key => $loc_value) {
    $loc_value['voyagerLocationCode'] = $loc_key;
    array_push($location_codes, $loc_value);
  }

  file_put_contents(__DIR__.'/../log/locations.json', json_encode($location_codes));

  return new JsonResponse($location_codes);
});

$app->get('/locations', function() use ($app) {
   return $app['twig']->render('locations.html.twig', array(
    'locations' => $app['locations.list'],
    'environment' => $app['environment']['env'],
    'title' => "Active Voyager Locations"
  ));
});

/*
 * Route to direct queries to Pulfa
 *
 */

$app->get('/pulfa/{index_type}', function($index_type) use($app) {
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

/* Wrapper for Primo Brief Search API Call
 *
 * EL Commons http://www.exlibrisgroup.org/display/PrimoOI/Brief+Search
 *
 * Route Variable
 *
 * {index_type} scope the search to a particular field
 * choices issn|isbn|lccn|oclc|title|any|lsr05|creator
 *
 * lsr05 is call number
 *
 * Possible Query Parameters
 *
 * @query - string to search for
 *
 * @limit - can be contains, exact, or begins_with see $app['primo_server_connection']['default.search'] for default
 * NOTE "begins_with" can only be used with the title parameter otherwise an error can be thrown
 *
 * @scopes - see $app['primo_server_connection']['default.scope'] for default value
 * Example: .....?scopes=ENG,MUSIC - search only english and music libraries
 *
 * @format - see $app['primo_server_connection']['default.search'] for default value
 * Example: /find/title/journal+of+politics?format=journals - get only items with the journals facet back
 *
 */

 $app->get('/pulsearch/{index}', function($index) use ($app) {
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
  $client = new Blacklight($app['blacklight.host'], 'catalog');
  $response = $client->query($query, $index_type);
  $blacklight_response = BLResponse::getResponse($response);
  $blacklight_response["more"] = $app['blacklight.host'] . "/catalog?" . "search_field=" . $index_type . "&q=" . $query . "&utf8=%E2%9C%93";
  return new JSONResponse($blacklight_response, 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
 })->assert('index_type', '(any|issn|isbn|title)');


 $app->get('/find/{index_type}', function($index_type) use($app) {

  if($app['request']->get('query')) {
    $query = $app['request']->get('query');
  } else {
    return "No Query Supplied";
  }

  if($app['request']->server->get('HTTP_REFERER')) {
    $referer = $app['request']->server->get('HTTP_REFERER');
  } else {
    $referer = "Direct Query";
  }

  if($app['request']->get('number')) {
    $result_size = $app['request']->get('number');
  } else {
    $result_size = $app['primo_server_connection']['num.records.brief.display'];
  }

  if($app['request']->get('scopes')) {
    $scopes = explode(",", $app['request']->get('scopes'));
  } else {
    $scopes = $app['primo_server_connection']['default.scope'];
  }
  if($app['request']->get('limit')) {
    $operator = $app->escape($app['request']->get('limit'));
  } else {
    $operator = $app['primo_server_connection']['default.search'];
  }
  if($app['request']->get('format')) {
    $format_facet = "facet_rtype,exact," . $app['request']->get('format');
  }

  if($app['request']->get('subject')) {
    $subject_facet = "facet_topic,exact," . $app['request']->get('subject');
  }

  $primo_query = new PrimoQuery($query, $app->escape($index_type), $operator, $scopes, $result_size);
  if(isset($format_facet)) {
    $primo_query->addFacet($format_facet);
  }
  if(isset($subject_facet)) {
    $primo_query->addFacet($subject_facet);
  }
  $search_results = $app['primo_client']->doSearch($primo_query);
  if(preg_match('/MESSAGE=\"Unauthorized access\"/', $search_results)) {
    return new Response("Unauthorized Access", 403, array('Content-Type' => 'text/plain'));
  }
  if ($search_results) {
    $response = new PrimoResponse($search_results, $app['primo_server_connection']);
    $deep_link = new SearchDeepLink($query, $app->escape($index_type), $operator, $app['primo_server_connection'], 'location', $app['primo_server_connection']['default.scope'], $primo_query->getFacets());
    $response_data = array(
      'query' => $app->escape($query),
      'number' => $response->getHits(),
      'more' => $deep_link->getLink(),
      'records' => $response->getBriefResults(),
      );
    $app['monolog']->addInfo("Index Query:" . $query . "\tREFERER:" . $referer);
  } else {
    $response_data = array("no results available at this time");
  }
  return new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public',));
})->assert('index_type', '(issn|isbn|lccn|oclc|title|any|lsr05|lsr07|creator)'); // should this be a list of possible options from the

return $app;
