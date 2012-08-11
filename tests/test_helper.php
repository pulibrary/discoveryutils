<?php
  //require_once __DIR__.'/../vendor/silex.phar'; // include silex
  require_once __DIR__.'/../vendor/autoload.php'; 
  set_include_path(get_include_path() . PATH_SEPARATOR . $basepath);

  // Let's just load all PHP files in the classes directory in case
  // we need them.
  foreach (glob($basepath.'*.php') as $filename) {
    require_once(realpath($filename));
  }
?>
