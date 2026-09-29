<?php
session_start();
require_once __DIR__ . "/../config/db.php";

// para dali na lang tawagin sa bawat page
function isLoggedIn(){
	if(isset($_SESSION['user_id'])){
		return true;
	}
	return false;
}

function checkLogin(){
	if(!isLoggedIn()){
		header("Location: login.php");
		exit;
	}
}

function checkAdmin(){
	checkLogin();
	if($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'organizer'){
		echo "Access denied. Admins only.";
		exit;
	}
}
