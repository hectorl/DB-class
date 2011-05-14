<?php

	require('class.DB.php');

	define('HOST', 'localhost');
	define('USER', 'db_class');
	define('PASS', 'DQ82tAYWrPteWjyQ');
	define('_DB_', 'proofs');

	$db = DB::init();
	
	$db->delete();
	$db->tables('articles');
	$db->where('id = 2');

	echo ($db->execute(true)) ? 'success!' : 'error!';