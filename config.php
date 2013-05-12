<?php

return array(
	// Default controller name
	'defaultController'=>'addresses',

	// Components classes configuration params
	'componentsConfig'=>array(
		// Db connection. Look at components/Db.php
		// and look at PDO docs: http://php.net/manual/en/book.pdo.php
		'db'=>array(
			'class'=>'DbMySQLi',
			'dsn'=>'mysql:dbname=rest;host=127.0.0.1;charset=utf8',
			'user'=>'root',
			'password'=>'root',
		),
		'request'=>array(
			'class'=>'Request',
		),
	),
);
