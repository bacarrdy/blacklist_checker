<?php

date_default_timezone_set("Europe/Vilnius");

require (__DIR__).'/libs/flight/Flight.php';
require (__DIR__).'/libs/database.php';
require (__DIR__).'/libs/utils.php';

Flight::set('baseOfUrl', "/blacklist/");

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "secret");
define("DB_DB", "blacklist");

Flight::register('db', 'Database', array(DB_USER, DB_PASS, DB_HOST, DB_DB, true));
Flight::register('utils', new Utils());

Flight::set('flight.views.path', (__DIR__).'/views');

require (__DIR__).'/controlles/home.php';
require (__DIR__).'/controlles/search.php';

$HomeController = new HomeController();
$SearchController = new SearchController();

Flight::route('/', array($HomeController, 'view'));
Flight::route('/home', array($HomeController, 'view'));
Flight::route('/search', array($SearchController, 'view'));
Flight::route('POST /ipHistory', array($HomeController, 'ipHistory'));

Flight::start();
?>
