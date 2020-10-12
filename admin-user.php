<?php
use \Hcode\Model\User;
use \Hcode\PageAdmin;

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


?>