<?php 
include("/includes/header.php");
if(is_standalone()) confirm_logged_in(); 

if (isset($_GET['deleteuser'])) {
	try {
		$result = $sonic->("DELETE FROM users WHERE username='".$_GET['deleteuser']."'");
	}
	catch(PDOException $e) {
		echo "User could not be removed: ".$e->getMessage();
		die;
	}
	echo "User deleted"; 
	
}
else if (isset($_POST['clearcurrent'])) {
	try {
		$result = $sonic->query("UPDATE users SET current = ''");
		if($result) echo "Now Playing cleared";
	}
	catch(PDOException $e) {
		echo $e->getMessage();
		die;
	}
}
// START FORM PROCESSING
else if ((isset($_POST['username'])) && (isset($_POST['password']))) { // Form has been submitted.
	$errors = array();
	// perform validations on the form data
	$required_fields = array('username', 'password');
	$errors = array_merge($errors, check_required_fields($required_fields, $_POST));
	$fields_with_lengths = array('username' => 30, 'password' => 30);
	$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$hashed_password = sha1($password);
	
	if ( empty($errors) ) {
	try {
		$query = "INSERT INTO users (
							username, hashed_password
						) VALUES (
							'{$username}', '{$hashed_password}'
						)";
		$result = $sonic->query($query);
		if ($result) {
			echo "The user was successfully created.";
			if(isset($_POST['admin']) && $_POST['admin'] == 'true') {
				$result = $sonic->query("UPDATE users SET admin = 'true' WHERE username='{$username}'");
			}
		} else {
			echo "The user could not be created.<br />";
		}
	}
	catch(PDOException $e) {
		echo $e->getMessage();
		die;
	}
	} else {
		if (count($errors) == 1) {
			echo "There was 1 error in the form.";
		} else {
			echo "There were " . count($errors) . " errors in the form.";
		}
	}
} else { // Form has not been submitted.
	$username = "";
	$password = "";
}
?>
