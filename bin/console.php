<?php
require_once('vendor/autoload.php');

$app = require dirname(__FILE__).'/../src/app.php'; //bootstrap the app

use PrimoServices\Console\GreetCommand;
use PrimoServices\Console\LogCheck;
use Symfony\Component\Console\Application;
 
$console = new Application();
$console->add(new GreetCommand); 
$console->add(new LogCheck);
$console->run();
