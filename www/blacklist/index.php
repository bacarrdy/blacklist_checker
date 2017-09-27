<?php
require (__DIR__).'/libs/flight/Flight.php';
require (__DIR__).'/libs/database.php';

Flight::set('baseOfUrl', "/blacklist/");

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "databasepassword");
define("DB_DB", "blacklist");

Flight::register('db', 'Database', array(DB_USER, DB_PASS, DB_HOST, DB_DB, true));

Flight::set('flight.views.path', (__DIR__).'/views');

require (__DIR__).'/controlles/home.php';

$HomeController = new HomeController();

Flight::route('/', array($HomeController, 'view'));
Flight::route('POST /ipHistory', array($HomeController, 'ipHistory'));

Flight::start();
?>
