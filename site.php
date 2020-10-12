<?php
use \Hcode\Page;
use Hcode\Model\Category;
use Hcode\model\Products;

$app->get('/', function() {
	$products = Products::listAll();
	$page = new Page();
	$page->setTpl('index',[
		'products'=>Products::checkList($products)
	]);
});


$app->get("/categories/:idcategory",function($idcategory){
	$category = new Category();
	$category->get((int)$idcategory);
	var_dump($category->getValues());
	var_dump($category->getProducts());
	$page = new Page();
	$page->setTpl("category",[
		'category'=>$category->getValues(),
		'products'=>Products::checkList($category->getProducts()),
	]);
});
