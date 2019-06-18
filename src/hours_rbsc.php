<?php
use Hours\Day as Day;
use Symfony\Component\HttpFoundation\Response;

$app['hours.base'] = $app['environment']['app_base_url'];
$app['hours.daily'] = 'hours';

$hours = $app['controllers_factory'];
$hours->get('/', function(\Silex\Application $app) {
  $day_client = new Day($app['hours.base'], $app['hours.daily'] );
  $daily_hours = $day_client->getDailyHoursByLocation();
  return new Response(json_encode($daily_hours), 200, array('Content-Type' => 'application/json'));
});

return $hours;
?>