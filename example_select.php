<?php

	require('class.DB.php');

	define('HOST', 'localhost');
	define('USER', 'db_class');
	define('PASS', 'DQ82tAYWrPteWjyQ');
	define('_DB_', 'proofs');

	$db = DB::init();
	
	$db->select();
	$db->tables('articles');
	$db->fields();

	$res = $db->execute(true);
	
	echo '<pre>';
	var_dump($res);
	echo '</pre>';
