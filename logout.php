<?php
	//start session for Timeclock
	session_start();
	
	//destroy session for Timeclock
	session_destroy();
	
	//Redirect to index page
	header("Location: index.php");
?>
