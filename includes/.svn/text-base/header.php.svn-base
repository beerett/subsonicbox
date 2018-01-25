<?php
/** 
 * @file    header.php - Handles all php header information
 *
 * @version Sep-29-2010 
 * @todo    - This file needs to be the backbone of the php code the main goal is to minimize php coding by reusing code
 *                                            - There needs to be a check if setup has run when running on the jetty engine
 *          
 */
global $boolRunSetup;
if (!session_id()) session_start();
include_once(getcwd()."/includes/functions.php");
if(is_standalone()) { //check environment
    if (is_file(getcwd()."/includes/settings.php")) { //setup has run
		$boolRunSetup = false; //defined before settings.php, connection.php (which checks the settings constants) can change this variable
		include_once(getcwd()."/includes/settings.php"); 
        include_once(getcwd()."/includes/connection.php");
    }
    else {
        //setup has not been run
        $boolRunSetup = true;
	}
	
    include_once(getcwd()."/includes/login_handler.php");
}
else {
	if (is_file(getcwd()."/includes/settings.php")) { //setup has run

	include_once(getcwd()."/includes/settings.php"); 

	if (is_file(getcwd()."/folders.cfg")) { //check if the setup has been run
		if(isset($_SESSION['profileid']) && !isset($profileid)) {
			$profileid = trim($_SESSION['profileid']);
			$data = unserialize(file_get_contents(getcwd()."/profiles/$profileid.cfg"));
			$username = $data['username'];
			$password = str2Hex($data['password']);
			define('CREDS',"u=$username&p=enc:$password&v=1.2.0&c=bologna");
			define('USERNAME',"$username");
		
			}
				$boolRunSetup = false;
		}              
	}
	else {
		//setup has not been run
		$boolRunSetup = true;
	}
}
if(isset($_POST['profileid'])) {
echo "<div style='margin:0px auto; width:500px;'><div class='ui-overlay'><div class='ui-widget-overlay'></div>
				<div class='ui-widget-shadow' style='position:absolute; top:200px; width:302px; height:275px;'></div></div>
				<div style='position:absolute; top:200px; height:253px; width:280px; padding: 10px;' class='ui-widget ui-widget-content'>
				<h2 style='margin-top:0px; padding-bottom:5px; border-bottom:1px solid #545454;'>Locating Profile...</h2>
				<div style='width:275px;' id='profbar'></div><div id='userinfo'>";
$file = $_POST['profileid'];
	if (file_exists(getcwd()."/profiles/$file.cfg")) {
			$_SESSION['profileid'] = trim($_POST['profileid']);
			
			echo "<p>Profile was found, using $file<br /> Reloading page.</p><script type='text/javascript'> $(document).ready(function(){ setTimeout('window.location.reload()',3000); }); ";
	}
	else { 
		
		echo "A profile for the user '$file' was not created yet. Please choose one to use below, or create a new one. </div><div style='height:95px; padding:3px; overflow-y:auto;' id='selection'>";
		
		
		foreach (glob("profiles/*.cfg") as $filename) {
			echo "<input type='radio' name='cfg'>".basename($filename,".cfg")."</input><br />";
		}
		echo "</div><button id='selectprof'>Select</button><button id='createprof'>Create New</button></div>";
	
	}
	
}
?>