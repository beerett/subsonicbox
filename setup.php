<?php
include(getcwd().'/includes/header.php');

$ip = $_SERVER['REMOTE_ADDR'];
$test = explode('.',$ip);
$loc =  range(17,32);

if ($test[0] == '192' || $test[0] == '10' || $ip = '127.0.0.1' || $test[0] == '172' && in_array($test[1],$loc)) {	
	include_once(getcwd().'/includes/functions.php');
	
	if (isset($_GET['setfolders']) && $_GET['setfolders'] == 'true') {
		$folder = $_GET['folder'];
		$folders = $_GET['folder'];
		$fh = fopen(getcwd().'/folders.cfg','w') or die("Can't open file");
		fwrite($fh,serialize($folders));
		fclose($fh);
		
		
		
		die;
	} 
	else if (isset($_GET['ping']) && $_GET['ping'] == 'true') {
		if (isset($_GET['proftest'])) {
			$juke = JUKE;
		
		}
		else {
			$juke = $_GET['juke'];
		}
		$username = $_GET['s_user'];
		$password = str2Hex(trim($_GET['s_pass']));
		$strFile = $juke."/rest/ping.view?u=$username&p=enc:$password&v=1.2.0&c=bologna";
		$arrContent = my_xml2array($strFile);
		if ($arrContent[0]['attributes']['status'] == 'ok') {
			echo "<img src='img/check.gif'> Connection to Subsonic OK";
		}
		else {                                    
			echo "<img src='img/x.gif'> Connection to Subsonic Failed";
			echo $strFile;
		}
		die;
	}
	else if (isset($_GET['profget'])) {
		$data = file_get_contents('profiles/brett.cfg');
		echo print_r(unserialize($data));
		die;
	}
	else if (isset($_GET['profile']) && $_GET['profile'] == 'true') {
		if (isset($_GET['create'])) {
			echo $_GET;
			if (!isset($_GET['username'])) die("Error: User not supplied");
			else {
				$sFile = getcwd().'/profiles/'.$_GET['username'].'.cfg';
				if (!file_exists($sFile)) {
					$fh = fopen($sFile,'w') or die("Can't open file");
					$username = $_GET['username'];
					$password = $_GET['password'];
					$repeat = $_GET['repeat'];
					$shuffle = $_GET['shuffle'];
					$timestamp = $_GET['timestamp'];
					$arr = $_GET;
					unset($arr['profile']);
					unset($arr['create']);
					$data = serialize($arr);
					fwrite($fh,$data);
					
					fclose($fh);
					echo "<img src='img/check.gif' /> Profile saved\n	";
					echo $data;
				}
			}
			die;	
		
		}
		else {
			
			echo "<h3>Profiles</h3>
						<div class='line'>
						<input id='profsing' type='radio' checked='checked' name='prof'>Single(default)</input><input id='profmult' type='radio' name='prof'>Multi</input>
						<div id='profholder'><div id='profsetup'>
						<div class='line'>
						<div class='text' info='Enter a valid subsonic username to use for this profile.'>Username</div>
						<input type='text' id='prof_user' value='' />
					</div>
					<div class='line'>
						<div class='text' info='Enter the corresponding password to the username.'>Password</div>
						<input type='password' id='prof_pass' value='' />
					</div></div></div><button id='proftest'>Test Login</button><button id='saveprof'>Save Profile</button><button style='position:relative;' id='finishsetup'>Finish</button>";
					die;
			
		}
		
	
	}
	else if (isset($_GET['basic']) && $_GET['basic'] == 'true') {
		echo	"<script type='text/javascript'>
					$(document).ready(function() {
						$('#hint').position({ my:'left top',at:'left bottom',of:'#setup',offset:'0 5' }).css('width','112px');\n
					});\n
				</script>";
		echo	"<div id='sform'>
				<div><form id='bla'>";
		if (is_standalone()) {
		$servsoft = $_SERVER['SERVER_SOFTWARE'];
			echo 	"Standalone<input checked='checked' id='standalone' type='radio' name='db_type'></input><br /><br /><img src='img/check.gif'> $servsoft was detected, setting script environment";
		}
		else {
			echo "Built-In<input checked='checked' id='jetty' type='radio' name='db_type'></input><br /><br /><img src='img/check.gif'> Jetty was detected, setting script environment";
		}
		$server = "http://".$_SERVER["SERVER_NAME"].':'.$_SERVER["SERVER_PORT"];
		$server1 = $server.dirname($_SERVER['SCRIPT_NAME']);
		echo "</form></div><br />
				<div id='fields'>
					
						
						<input type='hidden' id='server' value='$server1' />
					
					<div class='line'>
						<div class='text' info='Enter the subsonic server url.'>Subsonic server</div>
						<input type='text' id='juke' value='$server' />
					</div>
					<div class='line'>
						<div class='text' info='Enter a valid subsonic username to grab available music folders.'>Username</div>
						<input type='text' id='s_user' value='' />
					</div>
					<div class='line'>
						<div class='text' info='Enter the corresponding password to the username.'>Password</div>
						<input type='password' id='s_pass' value='' />
					</div>";
				
			
			echo "<button id='p1test' style='position:relative; top:5px; left:425px;'>Test</button><button id='p1next' style='position:relative; top:5px; left:427px;'>Next</button></div></div>";
			die;
	}
	
	else if(isset($_GET['setup']) && $_GET['setup'] == 'true') {
		$sFile = getcwd().'/includes/settings.php';
		$fh = fopen($sFile,'w') or die("Can't open file");
		$data = "<?php\n";
		fwrite($fh,$data);
		$db_type = $_GET['db_type'];
		$data = "define('DB_TYPE','$db_type');\n";
		fwrite($fh,$data);
		$server = $_GET['server'];
		if($server[strlen($server)-1] == chr(47)) {
			$data = "define('SERVER','$server');\n";
		}
		else { 
			$server = $server.chr(47);
			$data = "define('SERVER','$server');\n";
		}
		fwrite($fh,$data);
		$jukebox = $_GET['juke'];
		if($jukebox[strlen($jukebox)-1] == chr(47)) {
			$data = "define('JUKE','$jukebox');\n";
		}
		else {
			$jukebox = $jukebox.chr(47);
			$data = "define('JUKE','$jukebox');\n";
		}
		fwrite($fh,$data);
		$s_user = $_GET['s_user'];
		$s_pass = str2Hex($_GET['s_pass']);
		$data = "define('SCREDS','u=$s_user&p=enc:$s_pass&v=1.2.0&c=bologna');\n";
		fwrite($fh,$data);
		if ($db_type == 'standalone') {
			$data = "define('CREDS','u=$s_user&p=enc:$s_pass&v=1.2.0&c=bologna');\n";
			fwrite($fh,$data);
		}
		$data = "?>";
		fwrite($fh,$data);
		die;	

	} 
	else if (isset($_GET['scanfolders']) && $_GET['scanfolders'] == 'true') {
		$file = JUKE."rest/getMusicFolders.view?".CREDS;
		$arr = my_xml2array($file);
		die;
	} 
	else if (isset($_GET['rescan']) && $_GET['rescan'] == 'true') {
		
		include_once(getcwd().'/includes/settings.php');
		include_once(getcwd().'/includes/connection.php');
		$file = JUKE."rest/getMusicFolders.view?".SCREDS;
		$arr = my_xml2array($file);
		$i=0;
		while ($i<count($arr[0][0])-1)  {
			$id = $arr[0][0][$i]['attributes']['id'];
			$name = $arr[0][0][$i]['attributes']['name'];
			echo	"<div style='margin:3px; width:350px; height:20px;'>
						<div style='float:left; width:155px;' class='musicfolderid'>$id - $name</div>
						<div style='float:right;'>
							<input name='type$i' type='radio' class='music'>Music</input>
							<input name='type$i' type='radio' class='video'>Video</input>
						</div>
					</div>";
			$i++;
		}
		echo "<hr width='550px' size='1'><button id='p2back' style='position:relative; top:5px;'>Back</button><button style='position:relative; top:5px; left:450px;' id='p2finish'>Next</button>";
		
		die;
	} 
	

	echo	"<html>
				<head>
					<title>Supersonic Setup</title>
					<link type='text/css' href='css/setup.css' rel='stylesheet' />

		
					<script type='text/javascript' src='js/jquery-1.4.2.min.js'></script>
					<link type='text/css' href='css/custom-theme/jquery-ui-1.8.2.custom.css' rel='stylesheet' />	
					<script type='text/javascript' src='js/jquery-ui-1.8.2.custom.min.js'></script>
					<script type='text/javascript' src='js/setup.js'></script>
					<script type='text/javascript' src='js/colortip-1.0-jquery.js'></script>
					<link rel='stylesheet' type='text/css' href='css/colortip-1.0.css'/>";
					if(is_standalone()) { echo "<script type='text/javascript'>var standalone = true;</script>"; }
					else { echo "<script type='text/javascript'>var standalone = false;</script>"; }
if (defined('SCREDS') && !is_file(getcwd().'/folders.cfg')) { echo "<script type='text/javascript'>var setupcreds = 'folders';</script>";  }
else if (defined('SCREDS') && is_file(getcwd().'/folders.cfg') && (!is_standalone())) { echo "<script type='text/javascript'>var setupcreds = 'profile';</script>"; }
	
	echo "
				</head>
				<body color='#fff' bgcolor='#000'>
					<div id='hint'></div>
					
					<div id='container'>
						<div id='menu'>
							<span style='display:inline; font-size:20px; float:left;'>
								<img src='img/subsonic.png' />
								<span style='position:relative; font-size:20px; top:-8px;'> Setup</span>
							</span>
							<button id='profiles'>profiles</button>
							<button id='foldersetup'>config folders</button>
							
							<button id='setup'>basic setup</button>
	
						</div>
						<div id='main'>";
	
	
	if (is_file(getcwd().'/includes/settings.php')) {        
		include_once(getcwd().'/includes/settings.php');

		if ((!defined('SERVER')) || SERVER == '' || (!defined('JUKE')) || JUKE == '' || (!defined('SCREDS')) || CREDS == '') {
			echo	"<script type='text/javascript'>\n
							$(document).ready(function() {\n
							$('#status').html(\"There seems to be an issue with some variables, please rerun basic setup.\");\n
							$('#hint').position({ my:'left top',at:'left bottom',of:'#setup',offset:'0 5' }).css('width','112px');\n
						});\n
					</script>";
		} 
		
		
		
		else {
			if (defined('SCREDS')) { 
				
				if (function_exists('curl_init')) {
					echo	"<br /><img src='img/check.gif' /> cURL is installed<br />"; 
				}
				else { 
					echo	"<br /><img src='img/x.gif' /> cURL extension is not enabled, DI.fm streams will not work<br />";  
				}
				
				$file = JUKE."rest/ping.view?".CREDS;
				$arr = my_xml2array($file);
			
				if($arr[0]['attributes']['status'] == 'ok') {
					echo	"<img src='img/check.gif' /> Connection to Subsonic server OK<hr size='1' />";
				} 
				else {
					echo	"<img src='img/x.gif' /> Connection to Subsonic server failed. ";
				}
				
				echo	"Local server ip/port:".SERVER."<br />
					Subsonic server/port:".JUKE."<br />
					<hr size='1'>";
				
				
				$a = explode('&', SCREDS);
				$i = 0;
				while ($i < count($a)) {
					$b = explode('=', $a[$i]);
					switch($i) {
						case 0: {
							$username = $b[1];
							echo "Subsonic username:".htmlspecialchars(urldecode($b[1]))."<br />";
							break;
						}
						case 1: {
							$password = $b[1];
							echo "Subsonic password:".htmlspecialchars(urldecode($b[1]))."<br />";
							break;						
						}
						case 2: {
							echo "API Version:".htmlspecialchars(urldecode($b[1]))."<br />";
							break;
						}
						case 3: {
							echo "Player name:".htmlspecialchars(urldecode($b[1]))."<br />";
							break;
						}
					}
					$i++;
				}

				if ($username != '' && $password != '' && defined('SERVER') && defined('JUKE') && defined('CREDS')) {
					echo	"<script type='text/javascript'>\n
								$(document).ready(function() {\n
									$('#status').append(\"<span>Script variables look setup, create the database if you haven't already, then configure your music/video folders\");\n								
								});\n
							</script>";
				} 
				else {
					echo	"<script type='text/javascript'>\n
								$(document).ready(function() {\n
										$('#status').html(\"There seems to be an issue with some variables, please rerun basic setup.\");\n
								});\n
							</script>";
					
				}
			} 
		}
	}
	else echo "<script type='text/javascript'>var setupcreds = 'setup';</script>";
	/* Settings.php does not exist
	*
	*
	*/
	
	

	echo	"</div>
			<div id='status'></div>";
	
	
	
	echo	"</div>
			</div>
			</div>
			</body>
			</html>";
	
} 
else {
	die(header("Location: /"));
}
?>