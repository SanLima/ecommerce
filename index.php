<?php
session_start();
/*
127.0.0.1       comercio.com.br
127.0.0.2       sandro.com.br
*/
require_once("vendor/autoload.php");

use \Slim\Slim;
//use  Apsys\Page;
use Apsys\PageAdmin;
use Apsys\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
	$page = new Page();
	$page->setTpl("index");
	//$sql = new Apsys\DB\Sql();
  //$res = $sql->select("SELECT * FROM tb_users");
  //echo json_encode($res);

});

$app->get('/admin', function() {

	User::VerifyLogin();

	$page = new PageAdmin();
	$page->setTpl("index");
});

$app->get('/admin/login', function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("login");
});

$app->post('/admin/login', function(){

	//$sql = new Apsys\DB\Sql();
  //$res = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(":LOGIN"=>$_POST['login']));
  //echo json_encode($res);


	User::login($_POST['login'], $_POST['password']);

	header("Location: /admin");
	exit;
});


$app->get('/admin/logout', function(){
	User::logout();
	header('Location: /admin/login');
	exit;
});
$app->run();






 ?>
