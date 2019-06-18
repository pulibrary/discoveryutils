<?php
$lhome = $app['controllers_factory'];
$lhome->get('/', function(\Silex\Application $app) {

   return $app['twig']->render('home.html.twig', array(
    'environment' => $app['environment']['env'],
    'title' => $app['environment']['title']
  ));
});

return $lhome;
?>