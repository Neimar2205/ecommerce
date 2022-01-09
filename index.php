<?php 

require_once("vendor/autoload.php");

use Slim\Slim;
use Hcode\Page;
use Hcode\PageAdmin;

$app = new Slim();

$app->config('debug', true);


$app->get('/', function() {    
	$page = new Page();
	$page->setTPL("indexhome");
});


$app->get('/admin', function() {    
	$page = new PageAdmin();
	$page->setTPL("indexadmin");
});

$app->run();

 ?>