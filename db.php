<?php
	$servername = "us-cdbr-iron-east-04.cleardb.net";
	$username = "be45dbe91334cd";
	$password = "bd990064";
	$dbname = "ad_7694f64354c8909";
	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$conn) {
	    die("Connection failed: " . mysqli_connect_error());
	} else {
		//echo "ALL GOOD";
	}
?>