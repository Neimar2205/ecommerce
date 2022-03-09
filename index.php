<?php 

session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\DB\Sql;

$app = new Slim();

$app->config('debug', true);


$app->get('/', function() {    
	$page = new Page();
	$page->setTPL("indexhome");
});
 

$app->get('/admin', function() {  
	User::verifyLogin();  
	$page = new PageAdmin();
	$page->setTPL("indexadmin");
});

$app->get('/admin/login', function() {    
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTPL("login");
});

$app->post('/admin/login', function() {  
	/* var_dump("post");
	exit; */
	//Aqui recebe as informações de login informadas na tela de login  
	User::login($_POST["login"], $_POST["password"]);
	
	//Aqui se os dados de acesso estiverem corretos, o fluxo e direcionado para a Home page da admin.
	header("Location: /admin");
	exit;
	
});

$app->get('/admin/logout', function(){
	User::logout();
	header("Location: /admin/login");
	exit;
});

$app->run();

 ?>