<?php
require_once('vendor/autoload.php');

$app = require dirname(__FILE__).'/../src/app.php'; //bootstrap the app

use Console\GreetCommand;
use Console\LogCheck;
use Console\SitemapCrawl;
use Symfony\Component\Console\Application;
 
$console = new Application();
$console->add(new GreetCommand); 
$console->add(new LogCheck);
$console->add(new SitemapCrawl);
$console->run();
