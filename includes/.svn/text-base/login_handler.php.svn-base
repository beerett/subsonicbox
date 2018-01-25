<?php
/** 
 * @file    login_handler.php - This file handles all login and logout calls
 *								it also checks if a user is logged in or not
 * @author  enflux
 * @version Aug-13-2010 
 * @todo    - Needs a better and safer login system
 *          
 */

// START FORM PROCESSING

if (isset($_POST['submit'])) { // Form has been submitted.
	$errors = array();
	// perform validations on the form data
	$required_fields = array('username', 'password');
	$errors = array_merge($errors, check_required_fields($required_fields, $_POST));
	//$fields_with_lengths = array('username' => 30, 'password' => 30);
	//$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$hashed_password = str2hex($password);
	//$hashed_password = $password;
	if ( empty($errors) ) {
		if (is_logged_in($username, $hashed_password)) {
			$_SESSION['username'] = $username;
			$_SESSION['password'] = $hashed_password;
			header("index.php");
		}
		else {
			$message = "Username or password incorrect.";
		}
	} 
	else { 
		if (count($errors) == 1) {
			$message = "There was 1 error in the form.";
		} else {
			$message = "There were " . count($errors) . " errors in the form.";
		}
	}
	
} 
else { // Form has not been submitted.
	if (isset($_GET['logout']) && $_GET['logout'] == 1) {
		$_SESSION = array();
		if(isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();

		$message = "You are now logged out.";
	} 
	elseif (isset($_SESSION['username'])) {
		/*$result = $sonic->query("SELECT * FROM users") or die("Jukebox not <a href='/setup.php'>setup</a>");
		$result = "SELECT * FROM users WHERE username = '".$_SESSION['username']."'";
		foreach($sonic->query($result) as $row) {
			if ($row['admin'] == 1) define('ADMIN','1');
			else 
		}*/
		//define('ADMIN','0');
	}
	$username = "";
	$password = "";
}
?>