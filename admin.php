<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;

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
 

$app->get('/admin/forgot', function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTPL("forgot");
});

$app->post('/admin/forgot', function(){
	$user = new User();
	$user = User::getForgot($_POST['email']);
	header("Location: /admin/forgot/sent");
	exit;
});

$app->get('/admin/forgot/sent', function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTPL("forgot-sent");
});

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

 ?>