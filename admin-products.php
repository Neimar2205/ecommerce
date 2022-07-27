<?php

use Hcode\DB\Sql;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app->get("/admin/products", function(){
	User::verifyLogin();
	$products = new Product();
	$products = Product::listAll();
	$page = new PageAdmin();
	$page->setTpl("products",["products"=>$products]);	
});

$app->get("/admin/products/create", function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("products-create");
});

$app->post("/admin/products/create", function(){
	User::verifyLogin();
	$product = new Product();
	$product->setData($_POST);
	$product->save();
	header("Location: /admin/products");
	exit;
});

$app->get("/admin/products/:idproduct", function($idproduct){
	User::verifyLogin();
	$product = new Product();
	$product->get((int)$idproduct);
	$page = new PageAdmin();
	$page->setTPL("products-update", array(
		"product"=>$product->getValues()
		));
});

$app->get("/admin/products/:idproduct/delete", function($idproduct){
	User::verifyLogin();
	$product =  new Product();
	$product->get((int)$idproduct);
	$product->delete();
	header("Location: /admin/products");
	exit;
});

$app->post("/admin/products/:idproduct",function($idproduct){
	User::verifyLogin();
	$product = new Product();
	$product->get((int)$idproduct);
	$product->setData($_POST);
	$product->save();
	$product->setPhoto($_FILES["file"]);
	//if(file_exists($_FILES['file']['tmp_name']) || is_uploaded_file($_FILES['file']['tmp_name'])){
    //$product->setPhoto($_FILES["file"]);
 	//}
	
	header("Location: /admin/products");
	exit;
});



 ?>