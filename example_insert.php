<?php

	require('class.DB.php');

	define('HOST', 'localhost');
	define('USER', 'db_class');
	define('PASS', 'DQ82tAYWrPteWjyQ');
	define('_DB_', 'proofs');

	$db = DB::init();

	$fields = array('date_insert' => date('Y-m-d H:i'),
				 'title' => 'Lorem Ipsum 2',
				 'body' => 'Nullam rutrum, orci vel euismod 
				 			viverra, orci odio rhoncus metus, ut 
				 			ullamcorper dui erat id massa. Vivamus 
				 			convallis, neque eu viverra sollicitudin, 
				 			libero neque fermentum turpis, ut rutrum 
				 			turpis arcu.',
				 'id_user' => 1);

	$db->insert();
	$db->tables('articles');
	$db->fields($fields);

	echo ($db->execute(true)) ? 'success!' : 'error!';
