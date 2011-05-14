<?php

	require('class.DB.php');

	define('HOST', 'localhost');
	define('USER', 'db_class');
	define('PASS', 'DQ82tAYWrPteWjyQ');
	define('_DB_', 'proofs');

	$db = DB::init();

	$fields = array('title' => 'Lorem Ipsum mola mucho');

	$db->update();
	$db->tables('articles');
	$db->fields($fields);
	$db->where('id = 1');

	echo ($db->execute(true)) ? 'success!' : 'error!';
