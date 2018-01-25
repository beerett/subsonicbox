<?php
/** 
 * @file    View.php - The main guts of the jukeboxscript
 *          handles all calls to subsonic, parses the xml
 *			then spits out the xhtml
 * @author  enflux
 * @version Aug-11-2010 
 * @todo    - Add handler for user defined custom streams(nyi in the main interface)
 *          
 */


include(getcwd()."/includes/header.php");

error_reporting (E_ALL ^ E_NOTICE);

function exception_handler($exception) {
	ob_start();
	print_r($GLOBALS);
	print_r($exception);
	file_put_contents('exceptions.txt', ob_get_clean(). "\n",FILE_APPEND);
}

set_exception_handler('exception_handler');


if($_POST['view'] == "settings") { 
	/** 
	 * CALLED:		When a user saves their config window after changing values(repeat,shuffle,timestamping)
	 * PARAMS:		shuffle,repeat1,timestamp,username
	 * PURPOSE:		Saves user preferences - shuffle,repeat,timestamp(chat) 
	 *				to be loaded when logged in through (view=prefs)
	 * TO DO:		Add functionality to theme selection here
	 */
	$shuf = trim($_POST['shuffle']);
	$repeat = trim($_POST['repeat1']);
	$timestamp = trim($_POST['timestamp']);
	$username = trim($_POST['user']);
	$_SESSION['supersonic-shuffle'] = $shuf;
	$_SESSION['supersonic-repeat'] = $repeat;
	$_SESSION['supersonic-timestamp'] = $timestamp;
} 


else if ($_POST['view'] == "addtoplaylist") { //create playlists
	$songid = $_POST['songid'];
	$name = $_POST['name'];
	$file = JUKE."rest/createPlaylist.view?".CREDS."&name=$name&$songid";
	$arr = my_xml2array($file);
	print_r($arr);
}
else if ($_POST['view'] == "delplaylist") { //delete a playlist
	$playlistid = $_POST['playlistid'];
	$file = JUKE."rest/deletePlaylist.view?".CREDS."&id=$playlistid";
	$arr = my_xml2array($file);
	echo	"Playlist removed";
}
else if ($_GET['view'] == "mode") { 
	/** 
	 * CALLED:		When a user loads the main window, or when the user clicks on 
	 *				the video/music radio button in the bottom left corner of the page
	 * PURPOSE:		Loads all music/video folders in the root tree based on the $_GET['type'] variable
	 *				
	 *				
	 */
	$type = trim($_GET['type']);
	$result = folderSelect($type);
	
	echo	"<ul>";
	foreach($result as $row){ 
	
		$id = $row['id'];
		$arr = my_xml2array(JUKE."rest/getIndexes.view?".CREDS."&musicFolderId=$id");
		$o=0;
		while ($o<count($arr[0][0])-2) {
			$i=0;
			while ($i<count($arr[0][0][$o])-2)
				{
					$id=$arr[0][0][$o][$i]['attributes']['id'];
					$name = $arr[0][0][$o][$i]['attributes']['name'];
					echo 	"<li><div level='0' class='album ui-state-default'><a title='Add Folder to Playlist' class='addfolder' ><img border='0' src='img/addfolderb.gif'></a><a class='clk' album=\"$name\" albumid='$id' href='#'>".$name."</a></div><div class='actrack'></div></li>";
					$i++;
				}
			$o++;
		}
		
	}
	echo 	"</ul>";
	unset($arr);
}
else if($_GET['view'] == "search") { 
	/**
	 * CALLED:		When the user searches, or pages through a search
	 * PURPOSE:		Allows the user to search their library folders
	 * PARAMS:		$keyword: Search term to look for
	 *				$offset:  Used for paging through results
	 *				$scope:   What to search in(album, artist, song, all)
	 */
	$keyword = urlencode(trim($_GET['keyword']));
	$scope = $_GET['scope'];
	$offset = $_GET['offset'];
	$file = JUKE."rest/search.view?".CREDS."&$scope=$keyword&count=10&offset=$offset";
	$arr = my_xml2array($file);
	$i=0;
	while($i < count($arr[0][0])-2){
		$seconds = $arr[0][0][$i]['attributes']['duration'];
		$mins = floor($seconds/60);
		$secs = $seconds%60;
		if ($secs<6) $secs=$secs.'0';
		elseif ($secs>5 && $secs<10) $secs='0'.$secs;
		$track = $arr[0][0][$i]['attributes']['track'];
		if ($track < 10) $track=' '.chr(32).$track;
		$id = $arr[0][0][$i]['attributes']['id'];
		$album = str_replace("'","''",$arr[0][0][$i]['attributes']['album']);
		$artist = $arr[0][0][$i]['attributes']['artist'];
		$title = $arr[0][0][$i]['attributes']['title'];
		$cover = $arr[0][0][$i]['attributes']['coverArt'];
		echo	"<div class='album_track'><div class='trackname'><a title='Add To Playlist' track='$title' class='padd search' time='$mins:$secs' cover='$cover' album='$album' duration='$seconds' artist='$artist' songid='$id' href='#'><img src='img/addb.png' alt='Add Track to Jukebox Queue' /></a><a title='Download this track' href='".JUKE."rest/download.view?".CREDS."&id=$id'><img src='img/down.png' alt='Download this track' /></a><a title='Play this track now' class='playnow' href='#'><img src='img/playb.png'></a><div class='track_num'></div><div class='ttext'>$artist - $title</div></div><div class='duration'>".$mins.':'.$secs."</div></div>";
		$i++;
	} 
	$total = $arr[0][0]['attributes']['totalHits'];
	$y=$total/10;
	$y++;
	echo	"<div id='pages'>";
	for($x=1; $x<$y; $x++) {
		$z=$x*10-10;
		echo	"<button style='margin-top:4px; margin-bottom:4px; display:inline;' class='spage' offset='$z'>$x</button>";
	}
	echo	"<span style='margin-top:3px; float:right;'>Total hits: $total</span>";
	echo	"</div>";
	unset($arr);

}
else if($_GET['view'] == "loadstream") {  
	/**
	 * CALLED:		When the user clicks on an actual stream inside the DI.fm selection box
	 * PURPOSE:		Scrapes the actual playlist file(.pls) from the "imported" view below
	 *				and pulls out the stream urls to be fed to the playlist
	 * PARAMS:		$_GET['url'] passed as $file: the stream file(.pls)
	 */
	$file = curl_get_file_contents($_GET['url']);
	$needle =  '/(?<!file\d=)(http:\/\/\w.*)/';
	preg_match_all($needle,$file,$results);
	$i=0;
	$genre = $_GET['genre'];
	while ($i<count($results[0])) {
		$url=$results[0][$i];
		echo	"<div class='newtrack stream' artist='Digitally Imported' track='$genre' url='$url' style='display: block;'><img src=".SERVER.$_GET['pic']."><div class='grip ui-icon ui-icon-grip-dotted-vertical'></div><div class='tholder'><div class='band'>Digitally Imported</div><div class='tracktext'>$genre</div></div><div class='duration'>~</div></div>";
		$i++;
	}
}
else if($_GET['view'] == "imported") {  
	/**
	 * CALLED: 		When the user clicks on the DI.fm button.
	 * PURPOSE:		Scrapes the Di.fm page for all 96K quality streams, and finds
	 *				the title of the stream inbetween the <bold> tags.  After that
	 *				it pushes them into separate divs which the user can select
	 *				
	 */
	$haystack = curl_get_file_contents('http://www.di.fm');
	$needle = "/(http:\/\/\w.*\.pls)(?=.*96k)|(?<=Now Playing on\s).*(?=<\/b>)/";
	preg_match_all($needle,$haystack,$results);
	$i=0;
	echo	"<div id='s1'>";
	while ($i<count($results[0])){
		$title=$results[0][$i+1];
		$pls=$results[0][$i];
		if ($i<36) echo	"<div class='streamgenre'><a href='#' title='$title' pls='$pls'>$title</a></div>";
		else if ($i==36) echo	"</div><div id='s2'><div class='streamgenre'><a href='#' title='$title' pls='$pls'>$title</a></div>";
		else if ($i>36) echo	"<div class='streamgenre'><a href='#' title='$title' pls='$pls'>$title</a></div>";
		$i+=2;
	} 
	echo	"</div>";
}
else if ($_GET['view'] == "randomalbum") {
	/** 
	 * PURPOSE:		grabs a list of $number random album , possibly for a future 
	 *				feature on the main page , similar to subsonics webui
	 */
	$number = $_GET['no'];
	$file = JUKE."rest/getAlbumList.view?".CREDS."&size=$number&type=random";
	$arr = my_xml2array($file);
	$i=0;
	while($i < count($arr[0][0])-1){
		echo	"<div class='ralbum'><img src='".JUKE."rest/getCoverArt.view?".CREDS."&size=50&id=".$arr[0][0][$i]['attributes']['coverArt']."' /><a href='#' aid=".$arr[0][0][$i]['attributes']['id']."' class='loadalbum'><div class='album_title'>".$arr[0][0][$i]['attributes']['title']."</div></a></div>";
		$i++;
	}
	unset($arr);
}
else if($_POST['view'] == "current") { 
	/**
     * CALLED:		Whenever a song is played
	 * PURPOSE:		Sets the users current song int he database so others 
	 *				can see it or click on the +/play to listen to it
	 * PARAMS:		currentsong: 	song title
	 *				current:		artist
	 *				duration:		duration of song
	 *				cover:			cover img hash
	 *				user:			username of person listening
	 *				songid:			hash of song that subsonic gives to all music
	 */
	//include getcwd().'/includes/connection.php';
	$song = str_replace("'","''",$_POST['currentsong']);
	$artist = str_replace("'","''",$_POST['current']);
	$songid = $_POST['songid'];
	$username = $_POST['user'];
	$duration = $_POST['duration'];
	$cover = $_POST['cover'];
	$username = $_POST['user'];
	/* try {
		if ($song == 'online') { 
			$result = $sonic->exec("UPDATE users SET current = '$song',duration = '',cover = '' WHERE username = '$username'"); 
		}
		else { 
			
			$result = $sonic->exec("UPDATE users SET current = '{$artist}',currentsong = '{$song}',currentid = '{$songid}',duration = '{$duration}',cover = '{$cover}' WHERE username = '{$username}'"); 	
		}
	}
	catch(PDOException $e) {
				echo $e->getMessage();
				die;
	}
	*/
}
else if($_GET['view'] == "live") {  
	/**
	 * CALLED:  	Set on a timer automatically by the jukebox, grabs a new set every 15s
	 * PURPOSE:		lists all songs currently being played by users connected
	 */
	
	
		$file = JUKE."rest/getNowPlaying.view?".CREDS;
		$arr = my_xml2array($file);
		$total = count($arr[0][0])-1;
		$user = 0;
		while($user < $total) {
			$dur = ceil($arr[0][0][$user]['attributes']['duration']/60);
			$minsago = $arr[0][0][$user]['attributes']['minutesAgo'];
			if($minsago <= $dur) {
				$title = $arr[0][0][$user]['attributes']['title'];
				$artist = $arr[0][0][$user]['attributes']['artist'];
				$id = $arr[0][0][$user]['attributes']['id'];
				$cover = JUKE."rest/getCoverArt.view?".CREDS."&size=200&id=".$arr[0][0][$user]['attributes']['coverArt'];
				$duration = $arr[0][0][$user]['attributes']['duration'];
				$username = $arr[0][0][$user]['attributes']['username'];
				$seconds = $duration;
				$mins = floor($seconds/60);
				$secs = $seconds%60;
				if ($secs<6) $secs=$secs.'0';
				elseif ($secs>5 && $secs<10) $secs='0'.$secs;
				echo	"<div class='livetrack'><a title='Add To Playlist' cover='$cover' artist=\"$artist\" track=\"$title\" class='ladd' time='$mins:$secs' duration='$duration' songid='$id' href='#'><img src='img/addb.png' alt='Add Track to Jukebox Queue' /></a><a title='Play this track now' class='playnow' href='#'><img src='img/playb.png'></a><b>$username</b> - <div class='liveartist'>$artist</div> - <div class='livesong'>$title</div><br />";
			}
			$user++;
		} 
		unset($arr);
	
}
else if($_GET['view'] == "playlists") { //get playlists
	$file = JUKE."rest/getPlaylists.view?".CREDS;
	$arr = my_xml2array($file);
	$i=0;
	while($i < count($arr[0][0])-1){
		$pid = $arr[0][0][$i]['attributes']['id'];
		echo	"<div class='playlisttitle'><a href='#' title='Delete Playlist' class='removepl' pid='$pid'><img border='0' src='img/deleteplaylist.gif' /></a><a href='#' pid='$pid' title='Load Playlist' class='loadplaylist'><img border='0' src='img/loadplaylist.gif' />".$arr[0][0][$i]['attributes']['name']."</a></div>";
		$i++;
	}
	unset($arr);
}
else if($_GET['view'] == "loadplaylist") {  //loads a playlist
	$playlistid = $_GET['id'];
	$file = JUKE."rest/getPlaylist.view?".CREDS."&id=$playlistid";
	$arr = my_xml2array($file);
	$i=0;
	while ($i<count($arr[0][0])-2)
	{
		$cover = $arr[0][0][$i]['attributes']['coverArt'];
		$seconds = $arr[0][0][$i]['attributes']['duration'];
		$mins = floor($seconds/60);
		$secs = $seconds%60;
		if ($secs<6) $secs=$secs.'0';
		elseif ($secs>5 && $secs<10) $secs='0'.$secs;
		$track = $arr[0][0][$i]['attributes']['track'];
		$id = $arr[0][0][$i]['attributes']['id'];
		$album = str_replace("'","''",$arr[0][0][$i]['attributes']['album']);
		$title = $arr[0][0][$i]['attributes']['title'];
		echo	"<div class='newtrack' style='display: block;' duration='$seconds' time='$mins:$secs' songid='$id'><img src=".JUKE."rest/getCoverArt.view?".CREDS."&size=200&c=wat&id=$cover><div class='grip ui-icon ui-icon-grip-dotted-vertical'></div><div class='tholder'><div class='band'>".$arr[0][0][$i]['attributes']['artist']."</div><div class='tracktext'>".$arr[0][0][$i]['attributes']['title']."</div></div><div class='duration'>$mins:$secs</div></div>";
		$i++;
	}
	unset($arr);
}

else if($_GET['view'] == "album") { 
	/**
	 * CALLED: 		Whenever a user clicks on an album
	 * PURPOSE:		Lists inner albums and tracks, also sorts album structure
	 *				according to a 'level' system and applies the attribute level=#
	 *				top level albums start at level 0, and go up from there.
	 *				
	 *				ex)
	 *				artist(level 0) / album 1(level 1)
	 *								/ album 2(level 1) / album (level 2)
	 *				Levels are only used client side to determine which albums should
	 *				be open at any given time, so that you dont have 10 albums at once.
	 *								
	 * 
	 * PARAMS:		id: subsonic id of the album the user clicks on
	 *				level: sent by the jukebox to determine what level the album is
	 *					   located on, and adds one to it
	 *					   
	 *				
	 */
	$albumid = $_GET['id'];
	$file = JUKE."rest/getMusicDirectory.view?".CREDS."&id=$albumid";
	$arr = my_xml2array($file);
	$i=0;
	$level = $_GET['level']+1;
	$cover = $arr[0][0][$i]['attributes']['coverArt'];
	echo	"<div id='cover'><img src=".JUKE."rest/getCoverArt.view?".CREDS."&size=200&id=$cover /></div>";
	echo	"<div class='tracklist'>";
	while ($i<count($arr[0][0])-2) {
		if($arr[0][0][$i]['attributes']['isDir']=="true") { //scripted catch for directories within directories , adds 'inneralbum' attr
			$id = $arr[0][0][$i]['attributes']['id'];
			$album = str_replace("'","''",$arr[0][0][$i]['attributes']['album']);
			$title = $arr[0][0][$i]['attributes']['title'];
			echo	"<div level='$level' class='album_track album inneralbum'><a class='addinner' albumid='$id'><img src='img/addfolderb.gif'></a><a inner='$i' album='$album' albumid='$id' href='#'>$title</a></div><div class='innertracks'></div>";
			$i++;
		}
		else {
			$seconds = $arr[0][0][$i]['attributes']['duration'];
			$mins = floor($seconds/60);
			$secs = $seconds%60;
			if ($secs<6) $secs=$secs.'0';
			elseif ($secs>5 && $secs<10) $secs='0'.$secs;
			$track = $arr[0][0][$i]['attributes']['track'];
			if ($track < 10) $track=' '.chr(32).$track;
			$id = $arr[0][0][$i]['attributes']['id'];
			$album = str_replace("'","''",$arr[0][0][$i]['attributes']['album']);
			$artist = $arr[0][0][$i]['attributes']['artist'];
			$title = str_replace($artist,'',$arr[0][0][$i]['attributes']['title']);
			//used to playing songs on the servers hardware, not used atm - echo "<div class='album_track'><div class='trackname'><a title='Add To Playlist' track='$title' class='padd' time='$mins:$secs' album='$album' duration='$seconds' artist='$artist' songid='$id' href=".JUKE."rest/jukeboxControl.view?".CREDS."&action=add&id=$id><img src='img/addb.png' alt='Add Track to Jukebox Queue' /></a><a title='Download this track' href=".JUKE."rest/download.view?".CREDS."&id=$id><img src='img/down.png' alt='Download this track' /></a><a title='Play this track now' class='playnow' href='#'><img src='img/playb.png'></a><div class='track_num'>".$track."</div><div class='ttext'>".$arr[0][0][$i]['attributes']['title']."</div></div><div class='duration'>".$mins.':'.$secs."</div></div>";
			echo	"<div class='album_track'><div class='trackname'><a title=\"Add To Playlist\" track=\"$title\" class=\"padd\" time=\"$mins:$secs\" album=\"$album\" duration=\"$seconds\" artist=\"$artist\" songid=\"$id\" href=\"#\"><img src='img/addb.png' alt='Add Track to Jukebox Queue' /></a><a title='Download this track' href='".JUKE."rest/download.view?".CREDS."&id=$id'><img src='img/down.png' alt='Download this track' /></a><a title='Play this track now' class='playnow' href='#'><img src='img/playb.png'></a><div class='track_num'>".$track."</div><div class='ttext'>$title</div></div><div class='duration'>".$mins.':'.$secs."</div></div>";
			$i++;
		}
	}
	echo	"</div>";
	unset($arr);
}
else if($_GET['view'] == "chat") { //get chat
	$file = JUKE."rest/getChatMessages.view?".CREDS;
	$arr = my_xml2array($file);
	$i = 0;
	$w = count($arr[0][0])-1;
	while ($i < count($arr[0][0])-1) {
		$message = explode(" ",$arr[0][0][$i]['attributes']['message']);
		$username = $arr[0][0][$i]['attributes']['username'];
		$msg1 = $arr[0][0][$i]['attributes']['message'];
		if(is_standalone()) {
			if($message[0] != '') { 
				print_r($message[0]."<div class='name'>".$message[2]." : </div>");
			} 
			else {
				print_r("<div class='name'>".$message[2]." : </div>");
			}
				
			for($d = 3;$d<count($message);$d++ ) {
				print_r($message[$d]." ");
			}	
		}
		else {
			echo "<div class='name'> $username : </div> $msg1";
		}
		echo	"<br />";
		$i++;
	}
}
else if($_GET['view'] == "chatsend") { //send chat text
	$nick = $_GET['nick'];
	$message = $_GET['message'];
	$o = chr(40);
	$c = chr(41);
	$msg2 = urlencode($o.date('g:i').$c.' '.$nick.' '.$message);
	$file = JUKE."rest/addChatMessage.view?".CREDS."&message=$msg2";
	file_get_contents($file);	
}
else if($_GET['view'] == "prefs") { 
	/**
	 * CALLED: 		By the client when the main jukebox is loaded
	 * PURPOSE:		Returns the values of shuffle, repeat , and timestamp in true/false
	 *				The client then turns on or off the buttons based on the user settings
	 *				ex response) true true true
	 *				This would turn on shuffle, repeat, and timestamping of chat
	 * TODO:		Add reading/writing of these into SESSION vars for the addon version
	 */

	echo $_SESSION['supersonic-shuffle'].','.$_SESSION['supersonic-repeat'].','.$_SESSION['supersonic-timestamp'];
	
}
else if($_GET['view'] == "addfolder") { 
	/**
	 * CALLED:		When a user clicks the plus sign to the left of the album name or
	 *				when a user right clicks on the album, and hits add to playlist
	 * PURPOSE:		Grabs the directory and all files below the current tree, and cycles through 
	 *				All nested files/folders until it reaches the end, adding every songid on the way.
	 * PARAMS:		id: the subsonic assigned albumid
	 *				
	 */
	$albumid = $_GET['id'];
	$file = JUKE."rest/getMusicDirectory.view?".CREDS."&id=$albumid";
	$arr = my_xml2array($file);
	$i=0;
	$e=0;
	$cover = $arr[0][0][$i]['attributes']['coverArt'];	
	while ($e<count($arr[0][0])-2) {
		if($arr[0][0][$i]['attributes']['isDir']=="true"){ //directory catch
			$i1=0;
			$albumid1 = $arr[0][0][$i]['attributes']['id'];
			$file1 = JUKE."rest/getMusicDirectory.view?".CREDS."&id=$albumid1";
			$arr1 = my_xml2array($file1);
			while ($i1<count($arr1[0][0])-2) { //grabs files inside it
				$cover = $arr1[0][0][$i1]['attributes']['coverArt'];	
				$seconds = $arr1[0][0][$i1]['attributes']['duration'];
				$mins = floor($seconds/60);
				$secs = $seconds%60;
				if ($secs<6) $secs=$secs.'0';
				elseif ($secs>5 && $secs<10) $secs='0'.$secs;
				$track = $arr1[0][0][$i1]['attributes']['track'];
				if ($track < 10) $track=' '.chr(32).$track;
				$id = $arr1[0][0][$i1]['attributes']['id'];
				$album = str_replace("'","''",$arr1[0][0][$i1]['attributes']['album']);
				$title = addslashes($arr1[0][0][$i1]['attributes']['title']);
				$artist = $arr1[0][0][$i1]['attributes']['artist'];
				echo 	"<div class='newtrack' duration='$seconds' num='$track' time='$mins:$secs' songid='$id'><img src='".JUKE."rest/getCoverArt.view?".CREDS."&size=200&c=wat&id=$cover' /><div class='grip ui-icon ui-icon-grip-dotted-vertical'></div><div class='tholder'><div class='band'>$artist</div><div class='tracktext'>$title</div></div><div class='duration'>$mins:$secs</div></div>";
				$i1++;
			}
			$i++;
		}
		else {
			while ($i<count($arr[0][0])-2) {  //grabs normal files within the first dir
				$seconds = $arr[0][0][$i]['attributes']['duration'];
				$mins = floor($seconds/60);
				$secs = $seconds%60;
				if ($secs<6) $secs=$secs.'0';
				elseif ($secs>5 && $secs<10) $secs='0'.$secs;
				$track = $arr[0][0][$i]['attributes']['track'];
				if ($track < 10) $track=' '.chr(32).$track;
				$id = $arr[0][0][$i]['attributes']['id'];
				$album = str_replace("'","''",$arr[0][0][$i]['attributes']['album']);
				$title = $arr[0][0][$i]['attributes']['title'];
				$artist = $arr[0][0][$i]['attributes']['artist'];
				echo	"<div class='newtrack' duration='$seconds' num='$track' time='$mins:$secs' songid='$id' name='$title'><img src='".JUKE."rest/getCoverArt.view?".CREDS."&size=200&c=wat&id=$cover' /><div class='grip ui-icon ui-icon-grip-dotted-vertical'></div><div class='tholder'><div class='band'>$artist</div><div class='tracktext'>$title</div></div><div class='duration'>$mins:$secs</div></div>";
				$i++;
			}
		}
		$e++;
	}
	unset($arr);
}
function curl_get_file_contents($URL)
{	
	$c = curl_init();
	$useragent='Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.4) Gecko/20100611 Firefox/3.6.4';
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_URL, $URL);
	curl_setopt($c, CURLOPT_USERAGENT, $useragent); 
	$contents = curl_exec($c);
	curl_close($c);

	if ($contents) return $contents;
	else return FALSE;
}
?>
