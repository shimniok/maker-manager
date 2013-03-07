<?php
function dbConnect() {
	$link = mysql_connect('localhost', 'test', 'supra29');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	echo "Connected successfully\n";

	//connect to the database
	mysql_select_db(test); 
	return $link;
}
?>
