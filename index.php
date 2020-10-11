<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;

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

$app->get('/admin/users', function() {
	User::verifyLogin();
	$users = User::listAll();
	$admin =new PageAdmin();
	$admin->setTpl('users',array(
		"users"=>$users
	));
});

$app->get('/admin/users/create', function() {
	User::verifyLogin();
	$admin =new PageAdmin();
	$admin->setTpl('users-create');
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
	$user = new User();
	$user->get((int)$iduser);
	$admin =new PageAdmin();
	$admin->setTpl('users-update',array(
		"user"=>$user->getValues()
	));
});

$app->post("/admin/users/create",function(){
	User::verifyLogin();
	$user = new User();
	$_POST['inadimin'] = isset($_POST['inadimin'])?1:0;
 	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [
 		"cost"=>12
 	]);

	$user->setData($_POST);
	$user->save();
	header("Location: /admin/users");
	exit;
});

$app->post('/admin/users/:iduser', function($iduser) {
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit;
});

$app->get("/admin/forgot",function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot");
});

$app->post("/admin/forgot",function(){
	$user = User::getForgot($_POST["email"]);
	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent",function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-sent");
});

$app->get("/admin/forgot/reset",function(){
	$user = User::validForgotDecrypt($_GET["code"]);
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-reset",array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/admin/forgot/reset",function(){
	$forgot = User::validForgotDecrypt($_POST["code"]);
	User::setForgotUser($forgot["idrecovery"]);
	$user = new User();
	$user->get((int)$forgot["iduser"]);
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		'cost'=>12
	]);
	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-reset-success");
});

$app->get("/admin/categories",function(){
	User::verifyLogin();
	$categories = Category::listAll();
	$page = new PageAdmin();
	$page->setTpl("categories",[
		"categories"=>$categories
	]);
});

$app->get("/admin/categories/create",function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("categories-create");

});

$app->post("/admin/categories/create",function(){
	$category = new Category();
	$category->setData($_POST);
	$category->save();
	header("Location: /admin/categories");
	exit;
});

$app->get("/admin/categories/:idcategory/delete",function($idcategory){
	$category = new Category();
	$category->get((int)$idcategory);
	$category->delete();
	header("Location: /admin/categories");
	exit;
});


$app->get("/admin/categories/:idcategory",function($idcategory){
	$category = new Category();
	$category->get((int)$idcategory);

	$page = new PageAdmin();
	$page->setTpl("categories-update",[
		'category'=>$category->getValues()
	]);
});

$app->post("/admin/categories/:idcategory",function($idcategory){
	$category = new Category();
	$category->get((int)$idcategory);
	$category->setData($_POST);
	$category->save();
	header("Location: /admin/categories");
	exit;
});

$app->run();

 ?>