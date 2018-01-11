<?php
/*
127.0.0.1       comercio.com.br
127.0.0.2       sandro.com.br
*/
require_once("vendor/autoload.php");

use \Slim\Slim;
//use  Apsys\Page;
use  Apsys\PageAdmin;

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
	$page = new PageAdmin();
	$page->setTpl("index");

	//$sql = new Apsys\DB\Sql();
  //$res = $sql->select("SELECT * FROM tb_users");
  //echo json_encode($res);

});

$app->run();






 ?>
