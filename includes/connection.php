<?php
	if ((!defined('SERVER')) || SERVER == '' || (!defined('JUKE')) || JUKE == '' || (!defined('CREDS')) || CREDS == '') {
		//echo 'There seems to be an issue with some variables, please rerun basic <a href=\'setup.php\'>setup</a>';
		//die;
		
		//setup has to run
		$boolRunSetup = true;
	}
	elseif (DB_TYPE == 'sqlite') { //connect/create the sqlite db
		try {
		
			$sonic = new PDO("sqlite:".getcwd()."/db/mp3.sqlite");
			$sonic->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e) {
			echo "There was an error connecting to or creating the database<br />: ".$e->getMessage();
			die;
		}
	}
	else if (DB_TYPE == 'mysql') { //connect to the mysql db
		if (!defined('DB_NAME') || DB_NAME == '' || !defined('DB_USER') || DB_USER == '' || !defined('DB_PASS') || DB_PASS == '' || !defined('DB_SERVER') || DB_SERVER == '') {
			echo "There seems to be an issue with some variables, please rerun basic <a href='setup.php'>setup</a>";
			die;
		}
		else { // 1. Create a database connection
			$dsn = 'mysql:dbname='.DB_NAME.';host='.DB_SERVER;
			$user = DB_USER;
			$password = DB_PASS;
			try {
				$sonic = new PDO($dsn, $user, $password);
			} 
			catch (PDOException $e) {
				echo 'Connection failed: ' . $e->getMessage();
			}
		}
	}
?>