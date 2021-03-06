<?php

use \Hcode\Model\User;
use \Hcode\PageAdmin;

$app->get('/admin/users', function () {
	User::verifyLogin();

	$page = (isset($_GET['page'])) ? $_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";

	if ($search != '') {
		$pagination = User::getPageSearch($search, $page);
	} else {
		$pagination = User::getPage($page);
	}


	$pages = [];
	for ($x = 0; $x < $pagination['pages']; $x++) {
		array_push($pages, [
			'href' => '/admin/users?' . http_build_query([
				'page' => $x + 1,
				'search' => $search
			]),
			'text' => $x + 1
		]);
	}

	$admin = new PageAdmin();
	$admin->setTpl('users', array(
		"users" => $pagination['data'],
		"search" => $search,
		"pages" => $pages,
	));
});

$app->get('/admin/users/create', function () {
	User::verifyLogin();
	$admin = new PageAdmin();
	$admin->setTpl('users-create');
});

$app->get('/admin/users/:iduser/delete', function ($iduser) {
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$user->delete();
	header("Location: /admin/users");
	exit;
});

$app->get('/admin/users/:iduser', function ($iduser) {
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$admin = new PageAdmin();
	$admin->setTpl('users-update', array(
		"user" => $user->getValues()
	));
});

$app->post("/admin/users/create", function () {
	User::verifyLogin();
	$user = new User();
	$_POST['inadimin'] = isset($_POST['inadimin']) ? 1 : 0;
	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [
		"cost" => 12
	]);

	$user->setData($_POST);
	$user->save();
	header("Location: /admin/users");
	exit;
});

$app->post('/admin/users/:iduser', function ($iduser) {
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit;
});

$app->get("/admin/users/:iduser/password",function($iduser){
	User::verifyLogin();

	$user = new User();
	$user->get((int)$iduser);
	
	$page = new PageAdmin();
	$page->setTpl('users-password', array(
		"user" => $user->getValues(),
		"msgError" => $user->getError(),
		"msgSuccess" => $user->getSuccess(),

	));
});

$app->post("/admin/users/:iduser/password", function($iduser){
	User::verifyLogin();

	if (!isset($_POST['despassword']) || $_POST['despassword']==='') {
		User::setError("Preencha a nova senha.");
		header("Location: /admin/users/$iduser/password");
		exit;
	}

	if (!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm']==='') {
		User::setError("Preencha a confirmação da nova senha.");
		header("Location: /admin/users/$iduser/password");
		exit;
	}

	if ($_POST['despassword'] !== $_POST['despassword-confirm']) {
		User::setError("Confirme corretamente as senhas.");
		header("Location: /admin/users/$iduser/password");
		exit;
	}

	$user = new User();
	$user->get((int)$iduser);
	$user->setPassword(User::getPasswordHash($_POST['despassword']));

	User::setSuccess("Senha alterada com sucesso.");
	header("Location: /admin/users/$iduser/password");
	exit;
});


