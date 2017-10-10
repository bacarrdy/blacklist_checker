<?php

date_default_timezone_set("Europe/Vilnius");

define("THISDIR", (__DIR__)."/");

require THISDIR.'libs/flight/Flight.php';
require THISDIR.'libs/database.php';
require THISDIR.'libs/utils.php';

Flight::set('baseOfUrl', "/blacklist/");

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "secretpassword");
define("DB_DB", "blacklist");

Flight::register('db', 'Database', array(DB_USER, DB_PASS, DB_HOST, DB_DB, true));
Flight::register('utils', new Utils());

Flight::set('flight.views.path', THISDIR.'views');

require THISDIR.'controlles/home.php';
require THISDIR.'controlles/search.php';
require THISDIR.'controlles/categories.php';

$HomeController = new HomeController();
$SearchController = new SearchController();
$CategoriesController = new CategoriesController();

Flight::route('/', function() { Flight::redirect('home'); });
Flight::route('/home', array($HomeController, 'view'));
Flight::route('/search', array($SearchController, 'view'));
Flight::route('/categories', array($CategoriesController, 'view'));
Flight::route('POST /categories/create', array($CategoriesController, 'create'));
Flight::route('POST /categories/get', array($CategoriesController, 'get'));
Flight::route('POST /categories/update', array($CategoriesController, 'update'));
Flight::route('POST /categories/delete', array($CategoriesController, 'delete'));
Flight::route('POST /ipHistory', array($HomeController, 'ipHistory'));

Flight::start();
?>
