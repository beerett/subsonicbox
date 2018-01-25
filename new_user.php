<?php 
require_once(getcwd().'/includes/header.php');
if (is_standalone()) { 
	confirm_logged_in();
	echo	"
	<table id='structure'>
		<tr>
			<td id='page'>
				<h2>Create New User</h2>
				
				<form id='userfunc'>
					<table>
						<tr>
							<td>Username:</td>
							<td><input id='unadd' type='text' name='username' maxlength='30' value='' /></td>
						</tr>
						<tr>
							<td>Password:</td>
							<td><input id='pwadd' type='password' name='password' maxlength='30' value='' /></td>
						</tr>
						<tr>
							<td colspan='2'><button style='float:right;' id='adduser' name='submit'>Create user</button></td>
						</tr>
					</table>
				</form>
				<h2>Delete User</h2>
				<select id='undel' name='username'></select><button style='margin:5px;' id='deleteuser'>Delete User</button>
				<div class='dresponse'></div>
			</td>
		</tr>
	</table>";
	try {
		$result = $sonic->query('SELECT * from users');
		foreach($result as $row){
			echo '<option>'.$row['username'].'</option>';
		}
	}
	catch(PDOException $e) {
		echo $e->getMessage();
		die;
	}
}
else {
	echo"
	<script type='text/javascript'>
	$(document).ready(function(){

	$('[title]').colorTip({color:'black'});
	$('#adduser').button();

	});</script>
	<table id='structure' style='width:400px;'>
		<tr>
			<td id='page' >
				<h2>Create New User</h2>
				
				<form id='userfunc'>
					<table width='350px'>
						<tr>
							<td>Username:</td>
							<td><input id='unadd' type='text' name='username' maxlength='30' value='' /></td>
						</tr>
						<tr>
							<td>Password:</td>
							<td><input id='pwadd' type='password' name='password' maxlength='30' value='' /></td>
						</tr>
						<tr style='height:10px;'></tr>
						<tr>
							<td style='width:130px;' class='subuseropts'>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='User is authenticated in LDAP'>LDAP Auth</span> <input type='checkbox' name='ldap' /></div>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='Adminstrator rights'>Admin</span><input type='checkbox' name='admin' /></div>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='Change settings or Password'>Settings</span><input type='checkbox' name='settings' /></div>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='Play files'>Stream</span><input type='checkbox' name='stream' /></div>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='Play files through the server'>Jukebox</span><input type='checkbox' name='jukebox' /></div>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='Download files'>Download</span><input type='checkbox' name='download' /></div>
							</td>
							<td style='width:100px;' class='subuseropts'>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='Upload access'>Upload</span> <input type='checkbox' name='upload' /></div>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='Create and delete playlists'>Playlists</span><input type='checkbox' name='playlists' /></div>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='Change cover art and tags'>CoverArt</span><input type='checkbox' name='coverart' /></div>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='Create and edit comments/ratings'>Comments</span> <input type='checkbox' name='comments' /></div>
								<div style='padding-left:20px; height:20px;'><span style='border-bottom:1px #545454 dotted;' title='Administrate Podcasts'>Podcasts</span> <input type='checkbox' name='podcasts' /></div>
								<div title='' style='padding-left:20px; height:20px;'></div>
							</td>
							
						</tr>
						<tr>
							<td></td>
							<td><button style='float:right;' id='adduser' name='submit'>Create user</button></td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>";
	
}
?>	










