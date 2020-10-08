<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);



$app->get('/', function() {
	$page = new Page();
	$page->setTpl('index');
	// echo $_SERVER["DOCUMENT_ROOT"];
});

$app->get('/admin', function() {
	User::verifyLogin();
	$admin =new PageAdmin();
	$admin->setTpl('index');
});


$app->get('/admin/login', function() {
	$admin =new PageAdmin([
		"header"=> false,
		"footer"=> false
	]);
	$admin->setTpl('login');
});

$app->post('/admin/login', function() {
	User::login($_POST["login"],$_POST["password"]);
	header("location: /admin");
	exit;
});


$app->get('/admin/logout', function() {
	User::logout();
	header("location: /admin/login");
	exit;
});


$app->run();

 ?>