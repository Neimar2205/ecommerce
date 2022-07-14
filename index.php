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
	//exit;
	//Aqui se os dados de acesso estiverem corretos, o fluxo e direcionado para a Home page da admin.
	header("Location: /admin");
	exit;
	
});

$app->get('/admin/logout', function(){
	User::logout();
	header("Location: /admin/login");
	exit;
});

$app->get('/admin/users', function() {  
	User::verifyLogin();  
	$users = User::listAll();
	$page = new PageAdmin();
	$page->setTPL("users",array(
		"users"=>$users
	));
});

$app->get('/admin/users/create', function() {  
	User::verifyLogin();  
	$page = new PageAdmin();
	$page->setTPL("users-create");
});

$app->get('/admin/users/:iduser/delete', function($iduser) {  
	User::verifyLogin(); 
	$user = new User();
	$user->get((int)$iduser);
	
	$user->delete();
	header("Location: /admin/users");
	exit;

});

$app->get('/admin/users/:iduser', function($iduser) {  
	User::verifyLogin();  
	$user =new User();
	$user->get((int)$iduser);
	$page = new PageAdmin();
	$page->setTPL("users-update", array(
		"user"=>$user->getValues()
	));
});

$app->post('/admin/users/create', function() {  
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);
	
	$user->save();

	header("Location: /admin/users");
	exit;

});

$app->post('/admin/users/:iduser', function($iduser) {  
	User::verifyLogin();  
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
	$user->get((int)$iduser);
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit;

});

$app->get('/admin/forgot', function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	//var_dump("Na rota get forgot no index");
	//exit;
	$page->setTPL("forgot");
});

//$app->post('/admin/forgot', function(){});
$app->post('/admin/forgot', function(){
	$user = new User();
	$user = User::getForgot($_POST['email']);
	//var_dump($user);
	//exit;

	header("Location: /admin/forgot/sent");
	exit;
});

$app->get('/admin/forgot/sent', function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	//var_dump("Sent");
	//exit;
	$page->setTPL("forgot-sent");
});

		 ///admin/forgot/reset
$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTPL("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	)); 
});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setFogotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = User::getPasswordHash($_POST["password"]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");

});


$app->run();

 ?>