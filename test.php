<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "test";
$conn = NULL;

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

//include("database.php");


function insertSQL($conn, $IDyearClass, $class, $gradYear, $time)
{
	$sqlQuerry = "INSERT INTO queue (IDyearClass, class, gradYear, time) VALUES ('$IDyearClass', '$class', $gradYear, '$time')";
	try {
		mysqli_query($conn, $sqlQuerry);
		echo "Class added";
	} catch (mysqli_sql_exception) {
		echo 'Something Wrong';
	}
}

insertSQL($conn, "2018y", "3.y", 2021, "18:30:00");


mysqli_close($conn);
