<?php
use Hours\Hours as Hours;
use Symfony\Component\HttpFoundation\Response;

$app['hours.base'] = $app['environment']['app_base_url'];
$app['hours.locations'] = 'services/voyager/libraries.json';
$app['hours.weekly'] = 'services/voyager/hours.json';

$hours = $app['controllers_factory'];
$hours->get('/', function(\Silex\Application $app) {
  $hours_client = new Hours($app['hours.base'], $app['hours.locations'], $app['hours.weekly']);
  $xml = $app['twig']->render('locations.xml.twig', array(
      'libraries' => $hours_client->getCurrentHoursByLocation(),
      'base_url' => $app['environment']['app_base_url'],
      'cur_month' => $hours_client->getCurrentMonth(),
  ));
  return new Response($xml, 200, array('Content-Type'=> 'application/xml'));
});

return $hours
?>