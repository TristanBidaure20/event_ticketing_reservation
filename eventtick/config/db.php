<?php
// database connection
// change these if di gumana sa localhost mo
$host = "sql110.infinityfree.com";
$dbuser = "if0_42951472";
$dbpass = "Tj543212345";
$dbname = "if0_42951472_db_eventtick";

$conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);

if(!$conn){
	die("Connection failed: " . mysqli_connect_error());
}
