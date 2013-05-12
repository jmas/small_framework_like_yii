<?php

define('BASE_DIR', dirname(__FILE__));
define('CONTROLLERS_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'controllers');

require_once('App.php');

try {
	$app = App::getInstance();
	$app->setConfig(require('config.php'));
	$app->run();
} catch (Exception $e) {
	echo json_encode(array(
		'error'=>true,
		'errorMessage'=>$e->getMessage(),
	));
}
