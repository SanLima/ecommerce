<?php
session_start();
/*
127.0.0.1       comercio.com.br
127.0.0.2       sandro.com.br
*/
require_once("vendor/autoload.php");

use \Slim\Slim;
use  Apsys\Page;
use Apsys\PageAdmin;
use Apsys\Model\User;
use Apsys\Model\Category;

$app = new Slim();

$app->config("debug", true);

$app->get("/", function() {
	$page = new Page();
	$page->setTpl("index");
	//$sql = new Apsys\DB\Sql();
  //$res = $sql->select("SELECT * FROM tb_users");
  //echo json_encode($res);

});

$app->get("/admin", function() {

	User::VerifyLogin();

	$page = new PageAdmin();
	$page->setTpl("index");

});

$app->get("/admin/login", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});

//LOGIN - LOGOUT
//[
$app->post("/admin/login", function(){

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;
});

$app->get("/admin/logout", function(){
	User::logout();
	header("Location: /admin/login");
	exit;
});

//LOGIN LOGOUT
//]

$app->get("/admin/users", function(){

	User::VerifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();
	$page->setTpl("users", array("users"=>$users));

});

$app->get("/admin/users/create", function(){

	User::VerifyLogin();
	$page = new PageAdmin();
	$page->setTpl("users-create");

});

$app->get("/admin/users/:iduser/delete", function($iduser){

	User::VerifyLogin();
	$user= new User();

	$user->get((int)$iduser);

	$user->delete();
	header('Location: /admin/users');
	exit;
});


$app->get("/admin/users/:iduser", function($iduser){

	User::VerifyLogin();
	$user = new User();
	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update", array("user"=>$user->getValues()
));

});

$app->post("/admin/users/create", function(){
	User::VerifyLogin();
	$_POST['inadmin']=(isset($_POST['inadmin']))?1:0;
	$user= new user();
	$user->setData($_POST);
	$user->save();
	header('Location: /admin/users');
	exit;


});

$app->post("/admin/users/:iduser", function($iduser){

	User::VerifyLogin();
	$_POST['inadmin']=(isset($_POST['inadmin']))?1:0;

	$user= new User();

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header('Location: /admin/users');
	exit;
});

$app->get("/admin/forgot", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot");

});

$app->post("/admin/forgot", function(){
	$user = User::forgot($_POST['email']);
	header('Location: /admin/forgot/sent');
	exit;
});

$app->get("/admin/forgot/sent", function(){

		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);

		$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){
		$user = User::validForgotDecrypt($_GET['code']);

		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);

		$page->setTpl("forgot-reset", array(
			"name"=>$user['desperson'],
			"code"=>$_GET['code']
		));

});

$app->post('/admin/forgot/reset', function(){
		$forgot = User::validForgotDecrypt($_POST['code']);
		//var_dump($_POST);

		User::setForgotUsed($forgot['idrecovery']);

		$user = new User();
		$user->get((int)$forgot['iduser']);

		$password	=	password_hash($_POST['password'], PASSWORD_DEFAULT, [
			"cost"=>12
		]);

		$user->setPassword($password);


		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]);

		$page->setTpl("forgot-reset-success");

});

$app->get("/admin/categories", function(){

	User::VerifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", [
		"categories"=>$categories
	]);

});

$app->get("/admin/categories/create", function(){

	User::VerifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");

});

$app->post("/admin/categories/create", function(){

	User::VerifyLogin();

	$categories = new Category();

	$categories ->setData($_POST);

	$categories->save();

	header('Location: /admin/categories');
	exit;

});


$app->get('/admin/categories/:idcategory/delete', function($idcategory){

	User::VerifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header('Location: /admin/categories');
	exit;

});


$app->get('/admin/categories/:idcategory', function($idcategory){

	User::VerifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", [
		"category"=>$category->getValues()
	]);

});

$app->post('/admin/categories/:idcategory', function($idcategory){

	User::VerifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');
	exit;
});


$app->run();
 ?>
