			<script type='text/javascript'>
			//			$(document).ready(function(){ $('#left,#rc,#menu,#container,#lmb,#albuminfo,#chat,#livelook,#playlist,#streams').draggable().resizable(); });
			</script>
			<div id="container">
				<div id='lmb'>
					<div id='menu'><button class='menubtn' id='home' onclick="home()"><div class='headerimgset'></div><div class="menutext">Home</div></button><button id='sbutton'><div class='headerimgset'></div><div class="menutext">Search</div></button><button id='plbut'><div class='headerimgset'></div><div class='menutext'>Playlist</div></button><button id='config'><div class='headerimgset'></div><div class="menutext">Config</div></button><button id='logout'><div class='headerimgset'></div><div class="menutext">Logout</div></button><?php if(ADMIN == '1') { echo "<button id='admin'><div class='headerimgset'></div><div class='menutext'>Admin</div></button>"; } else echo "<button id='refresh'><div class='headerimgset'></div><div class='menutext'>Refresh</div></button>"; ?></div>
					<div id="left" class="ui-corner-all ui-widget ui-widget-content">
						<div id="lhead" class="ui-corner-top ui-widget-header">Full Albums</div>
						<div id="leftinner" class="ui-widget-content"></div>
						<div id='leftfoot'>
							<form style='height:16px;' id='modes'>
								<input id='rmusic' type="radio" name="mode" checked="checked" value="music" /> Music
								<input id='rvideo' type="radio" name="mode" value="video" /> Video
							</form> 
							<div class='preload ui-helper-hidden'>
								<img src='img/25.gif' />
							</div>
						</div>
					</div>
				</div>
				<div id='rc' class='ui-helper-clearfix'>
					<div id="vidplayercont"><div id="vidplayer"></div></div>
					<div id='streams'>
						<div id='buttons'>
							<button id='AJ' class='streambutton'>
								<div class='streamimgset'></div>
								<div class="menutext">AJ</div>
							</button>
							<button id='xdi' class='streambutton'>
								<div class='streamimgset'></div>
								<div class="menutext">DI.fm</div>
							</button>
						</div>
						<form id='newsearch'>
							<div id='searchbar' class='ui-widget'>
								<input id='searchbartext' type='text' />
								<button id='searchbutton'>Search</button><br />
							</div>
						</form>	
					</div>
				<div id="nowplaying" class='ui-widget-content'></div>
				<div id="nowplayingvideo"></div>
				<div id="chat" class="ui-corner-all ui-widget-content">
					<div id="chead" class="ui-widget-header ui-corner-top">Chat - <?php echo $_SESSION['username']; ?></div>
					<div class="text"></div>
					<form id='chatsub'>
						<input name='name' type='hidden' value='<?php echo $_SESSION['username']; ?>' />
						<div id="chat-buttons">
							<div id="chat-message" class="ui-widget">
								<input id="cmessage" type='text' name="message" />
							</div>
						</div>
					</form>
				</div>
				<div id='buttonbox'>
					<div id='controls'>
						<button id='cback'>Back</button>
						<button id='cplaypause'>Play/Pause</button>
						<button id='cstop'>Stop</button>
						<button id='cnext'>Next</button>
					</div>
					<div id='cplaylist'>
						<form style="display:inline;">
							<input id='cshuffle' type='checkbox' name='cshuffle' /><label for='cshuffle'>Shuffle</label>
							<input id='crepeat' type='checkbox' name='crepeat' /><label for='crepeat'>Repeat</label>
						</form>
					</div>
					<button id='mute'>Mute</button>
					<div id='volume'></div>
					<div id='amount'></div>
					<div id='playlistfunc'>
						<button id='savepl'>Save Playlist</button>
						<button id='clearpl'>Clear Playlist</button>
						<button id='loadpl' class='loadit'>Load Playlist</button>
					</div>
				</div>
				<div id="playlist" class="ui-corner-all ui-widget-content">
					<div id="phead" class="ui-corner-top ui-widget-header">Playlist</div>
					<div id="pl"></div>
					<div id='plfoot'></div>
				</div>
				<div id="albuminfo" class='ui-widget-content ui-corner-all'>
					<div id="album_name" class="ui-corner-top ui-widget-header">Welcome to the stash</div>
					<div id="tracks">
						<div class='rtext'>
							<br /><br />
						</div>	
					</div>
				</div>
				<div id='livelook' class='ui-corner-all ui-widget-content'>
					<div id='livehead' class='ui-widget-header ui-corner-top'>Who's Online</div>
					<div id='livetext'></div>
				</div>
				
			</div>
		</div>
		<div id="search" title="Search..">
			<form id='research'>
				<input style='margin-left:5px; margin-right:3px;' id='term' type='text' />
				<button id='searchnow'>Search</button>
				<input class='searchall' type='radio' checked='checked' name='stype'  value='any' />All
				<input class='searchartist' type='radio' name='stype' value='artist' />Artist
				<input class='searchalbum' type='radio' name='stype' value='album' />Album
				<input class='searchsong' type='radio' name='stype' value='song' />Song
			</form>
			<div id='results'></div>
			<button style='float:right; margin-top:4px;' id='done'>Done</button>
		</div>
		<div id='rtclk' class='ui-widget-content'>
			<a href='#' id='rtmv'><img src='img/move.gif'>Move</a><br />
			<a href='#' id='rtdl'><img src='img/dl.gif'>Download</a><br />
			<a href='#' id='rtrm'><img src='img/remove.gif'>Remove</a>
		</div>
		<div id='rtclka' class='ui-widget-content'>
			<a href='#' id='rtdla'><img src='img/dl.gif'>Download</a><br />
			<a href='#' id='rtqueuea'><img src='img/addfolderb.gif'>Add to Playlist</a><br />
		</div>
		<div id='distreams' title='Choose a DI.fm Stream to Load'></div>
		<div id='saveplaylist' title='Enter name for playlist'><br />
			<input id='plname' name='plname' type='text' />
			<button id='createpl'>Save</button>
			<div class='status'></div>
		</div>
		<div id="loadplaylist" class="ui-widget-content" title='Pleast select a Playlist'>
			<div class="playlists"></div>
			<div style='float:left;' id='plstatus'></div>
			<div class="close" style='float:right;'>
				<button style='margin-top:3px;'>Close</button>
			</div>
		</div>
		<div id='configd' title='Preferences' class='ui-widget'>
			<div id='configinner'>
				<div style="border-bottom:1px solid #545454;">
					<h3>Playlists</h3>
					<div id='configopts' style="padding-bottom:10px">
						<input id='shuffle' type='checkbox' name='shuffle' /><label for='shuffle'>Shuffle</label>
						<input id='repeat' type='checkbox' name='repeat' /><label for='repeat'>Repeat</label>
					</div>
				</div>		
				<div style="padding-bottom:10px; border-bottom:1px solid #545454; ">
					<h3>Chat</h3>
					<input id='timestamp' type='checkbox' name='timestamp' />
					<label for='timestamp'>Timestamp</label><br />
				</div>
				<div style="padding-bottom:10px; border-bottom:1px solid #545454; ">
					<h3>Stylesheet</h3>
					<SELECT NAME="stylesheet">
						<OPTION>thin.css</OPTION>
						<OPTION>nopad.css</OPTION>
					</SELECT>
				</div>	
				<button id='saveconfig'>Save</button>
			</div>
		</div>
			<?php if (ADMIN == 1) {
						echo "<div id='admind' title='Admin Panel'>
							<div id='tabs'>
								<ul>
									<li><a href='new_user.php'>Manage Users</a></li>
									<li><a href='admin_stuff.php'>General Options</a></li>
								</ul>
							</div>
						</div>";
					}
			?>
		<div id="playercont">
				<div id="player"></div>
		</div>
		</div>
		<div id='testuser'></div>
	