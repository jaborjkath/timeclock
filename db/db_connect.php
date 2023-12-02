<?php
error_reporting(E_ALL ^ E_DEPRECATED);

	function db_connect()
	{
		//local db
		$db_host = "localhost:3306";
		$db_username = "root";	
		$db_password = "";
	
		$db_connection = mysql_connect("localhost:3306", "root", "") or die (mysql_error());
		
		mysql_select_db("timeclock_project", $db_connection) or die (mysql_error());
		
		return $db_connection;
	}
	
	function db_disconnect($db_connection)
	{
		mysql_close($db_connection);
	}
?>
