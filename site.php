<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;

$app->get('/', function(){
    $products = Product::listAll();
    $page = new Page();
    $page->setTPL("indexhome", ['products'=>Product::checkList($products)]);

});

$app->get("/categories/:idcategory", function($idcategory){
	$category = new Category();
	$category->get((int)$idcategory);
	$page = new Page();
	$page->setTPL("category", ['category'=>$category->getValues(),
								'products'=>Product::checkList($category->getProducts())
							]);	
});

 ?>