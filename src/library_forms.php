<?php

$app['library.core'] = array(
   'host' => $core_base_path,
   'all.search.path' => "find/all",
   'db.search.path' => "research/databases/search"
 );
 
$forms = $app['controllers_factory'];

$forms->get('/libraryforms', function(\Silex\Application $app) {
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

return $forms;
?>