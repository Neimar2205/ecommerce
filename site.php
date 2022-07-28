<?php

use \Hcode\Page;
use \Hcode\Model\Product;

$app->get('/', function(){
    $products = Product::listAll();
    $page = new Page();
    $page->setTPL("indexhome", ['products'=>Product::checkList($products)]);

    //$page = new Page();
    //$page->setTPL("indexhome");

});

 ?>