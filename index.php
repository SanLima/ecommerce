<?php

require_once("vendor/autoload.php");

use \Slim\Slim;
use  Apsys\Page;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
	$page = new Page();
	$page->setTpl("index");

	//$sql = new Apsys\DB\Sql();
  //$res = $sql->select("SELECT * FROM tb_users");
  //echo json_encode($res);

});

$app->run();






 ?>
