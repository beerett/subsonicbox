			var pos = '';
			var dur = '';
			var min = '';
			var sec = '';
			
			function trim(string) { 
				return string.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
			}
			function inArray(elem,array) {
				for ( var i = 0, length = array.length; i < length; i++ ) {
					if ( array[i].toLowerCase() == elem.toLowerCase() ) {
						return i;
					}
				}
				return -1;
			}
			function diInfo() { //grabs di.fm streams
				$.ajax({
					url:"view.php",
					type:"GET",
					global:false,
					data:({ view:"imported" }),
					success: function(a) { 
						$('#distreams').html(a); 
					}
				});
			}
			function streamInfo(server) { //grabs the streams metadata
				$.ajax({
					url:"shoutcast_song_title.php",
					type:"GET",
					global:false,
					data:({ server:server }),
					success: function(a) { 
						$('#bottombox').text(a); 
					}
				});
			}
			//var player = null;	
			/*
			function playerReady(obj) { //jwplayer ready callback
				if ($('input:#rvideo').is(':checked')) {			
					player = document.getElementById(obj.id);
					list = new Array();
					$('#rc').css('height','555px');
					$('#nowplayingvideo').show();
					$('#nowplayingvideo').position({ 
						my:'left bottom', 
						at:'right bottom', 
						of:'#left', 
						offset:'9 0' 
					});
					$('#nowplayingvideo').position({ 
						my:'left bottom', 
						at:'right bottom', 
						of:'#left', 
						offset:'11 0' 
					}); // for some reason opera doesn't place it properly with one position() command..?
					$('#buttonbox').position({ 
						my:'left bottom', 
						at:'left top', 
						of:'#nowplayingvideo', 
						offset:'0 -1' 
					});
					$('#buttonbox').position({ 
						my:'left bottom', 
						at:'left top', 
						of:'#nowplayingvideo', 
						offset:'0 0' 
					});
				}
				else {
					player = jwplayer();
					list = new Array();
				}
				//addListeners(player);
				vol=parseInt(player.getConfig().volume);
			
			}
			*/
			function totalTime() {  //sets total playlist time, and tracks
				var b=0;
				$('.newtrack').each(function() { 
					b = b+parseInt($(this).attr('duration')); 
				});
				var hours=Math.floor(b/3600);
				var minutes=Math.floor(b/60)-(hours*60);
				var seconds=b-(hours*3600)-(minutes*60);
				var hs=' hour';
				var ms=' minute';
				var ss=' second';
				if (hours!=1) {hs+='s'}
				if (minutes!=1) {ms+='s'}
				if (seconds!=1) {ss+='s'}
				var tracks = $('#pl .newtrack').size();
				$('#plfoot').html("<span style='float:left;'>Total playing time:"+hours+hs+", "+minutes+ms+", "+seconds+ss+"</span><span style='float:right; margin-right:10px;'>"+tracks+" tracks</span>");
			}
			
			function oneShot() {
				ajaxFunction('asdf','chat');
			}
			function doit() {
				ajaxFunction('asdf','chat');
				setTimeout("doit()",5000);
			}
			function randTrack(a) { return Math.random()*a | 0; }
			function progUpdate(obj) { //progressbar callback
				var text='';
				for (itm in obj) {
					if (itm == 'position') {
						var pos = obj[itm];
						var dur = obj.duration;
						pos = Math.floor(pos);
						$('#slider').slider({ value:pos });
						min = Math.floor(pos/60);  
						sec = pos % 60;
						sec = Math.round(sec);
						if (sec < 10) { sec = '0'+sec; }
						text = min+':'+sec+' /';
						if ($('.np_pos').text() != text) { 
							$('.np_pos').text(min+':'+sec+' /');
						}
					}
				} 
			}
			function shuffleCheck() {	
				var b = randTrack($(".newtrack").size()-1);
				if ($('#cshuffle:checkbox:checked').val() == "on") {
					$('.newtrack').eq(b).click();
				}
				else if (parseInt($('.nowplaying').next().attr('duration')) > 0) {
					$('.nowplaying').next().click();
				}
				else if ($('#crepeat:checkbox:checked').val() == "on") {
					$('.newtrack:first').click();  
				}
			}
				
			function muteListener(obj) {
				currentMute = obj.state; 
				if (currentMute == true) { 
					$('#mute').button({ 
						icons: { 
							primary: 'ui-icon-volume-off' 
						}  
					}); 
				}  
				else { 
					$('#mute').button({ 
						icons: { 
							primary: 'ui-icon-volume-on' 
						} 
					}); 
				}
			}
			function streamStart(list) { 
			
			
				jwplayer().load(list).play(); 
				
					
			}
			function playpause(icon) { 
				$('#cplaypause').button({ 
					icons: { 
						primary: icon	
					} 
				}); 
			}
			function home() {
				$('#tracks').show();
				$('#playlist').hide();
				$('#leftinner .tracklist').hide();	
				$('#albuminfo').show()
				$('#chat').show();
				if ($('#nowplayingtext').length>0) { 
					$('#nowplaying').show(); 
				}
			}
			function showPlaylist() { 
				$('#playlist').show(); 
			}
			function urlencode(str) { 
				return escape(str).replace('+', '%2B').replace('%20', '+').replace('*', '%2A').replace('/', '%2F').replace('@', '%40');
			}
			function playlistStripe() {
				$('#pl .newtrack:odd').addClass("odd");
				$('#pl .newtrack:even').addClass("even");
			}
			function subsearch(term,area,count,offset) { 
				$.ajax({
					data:{ view:'search', 
						keyword:term, 
						scope:area, 
						offset:offset 
					},
					url:"view.php",
					success: function (a) {
						$('#results').html(a);
						$('#results .album_track:odd').addClass("odd");
						$('#results .album_track:even').addClass("even");
						$('#results .spage').button();
					}
				});
			}
			function viewsConnect(view,id,title) {
				$.ajax({ 
					data:{ 
						view:view, 
						id:id, 
						title:title
					},
					url:"view.php",
					success: function(a,b,c) { 
						$('#pl').append(a);
						playlistStripe();
						$('#pl .newtrack').fadeIn('fast');
						$('#leftinner .album active').click();
						totalTime();	
					}
				});
			}
			function sortList() {
				var mylist = $('#leftinner ul');
				var listitems = mylist.children('li').get();
				listitems.sort(function(a, b) {
					var compA = $(a).text().toUpperCase();
					var compB = $(b).text().toUpperCase();
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
				});
				$.each(listitems, function(idx, itm) { 
					mylist.append(itm); 
				});
			}
			function startUI() {
				$('#pl').sortable({   
					handle:'.grip',
					axis:'y',
					forcePlaceholderSize:'true',
					placeholder:'ui-state-highlight',
					containment:'parent',
					items:'.newtrack',
					helper: 'clone',
					opacity: '.5'
				});			
				$("#volume").slider({
					range: "min",
					value: 0,
					min: 0,
					max: 100,
					slide: function(event, ui) {
						$("#amount").text(ui.value);
						jwplayer().setVolume($('#volume').slider("value"));
					}
				}).show();
				
				$('#volume .ui-slider-range').addClass('ui-corner-all ui-state-hover');
			}
			function liveCheck() {
				$.ajax({
					type:"GET",
					url:"view.php",
					dataType:'html',
					data:({ view:'live' }),
					success: function(a){ 
						$('#livetext').html(a); 
					} 
				});
				setTimeout("liveCheck()",15000);
			}
			function getRand() {
				$.ajax({
					type:"GET",
					url:"view.php",
					dataType:'html',
					data:({ 
						view:'randomalbum', 
						size:10 
					}),
					success: function(a){ 
						$('.rtext').html(a); 
					} 
				});
			}
			function fetchList(type) {
				$.ajax({
					url:'view.php',
					data:({ view:'mode', type:type  }),
					type:'GET',
					dataType:'html',
					success: function(a) {  
						$('#leftinner').html(a); 
						sortList();
						var availableTags = new Array();
						$('#leftinner .clk').each(function(){ //autocomplete search
							artist = trim($(this).attr('album').substr(0,$(this).attr('album').indexOf('-',$(this).attr('album'))));
							album = trim($(this).attr('album').substr($(this).attr('album').indexOf('-',$(this).attr('album'))+1));
							if(inArray(artist,availableTags) == -1) availableTags.push(artist);
							if(inArray(album,availableTags) == -1) availableTags.push(album);
						});
						$("#term").autocomplete({ source: availableTags });
						$("#searchbartext").autocomplete({ source: availableTags });
					},
					error: function(a) { 
						$('.rtext').append(a);  
					}
				});
			}
			function createMoviePlayer(type,buffer,w,h) {
				if (type == "music") {
					$('#lhead').text('Music');
					$('#playercont').position({ 
						my:'top left', 
						at:'top left', 
						of:'#nowplaying', 
						offset:'-120 -50' 
					});   
					
					$('#bottombox').position({ 
						my:'left top', 
						at:'left bottom', 
						of:'#nowplayingvideo', 
						offset:'5 40'
					});
				}
				else { 
					$('#lhead').text('Videos');			
					
					
				}
				
				
			}
		
$(document).ready(function(){
				
	jwplayer("player").setup({
		flashplayer: "mediaplayer/player.swf",
		title:'Supersonic Media Player',
		controlbar:'false',
		icons:'false',
		plugins: {
			subeq: { 
				gain:'1', 
				displaymode:'decay', 
				barbasecolor:'02049c', 
				bartopcolor:'f58400', 
				reverseleft:'true' 
			}
		},
		height:90,
		width: 380,
		wmode:'transparent',
		events: {
			onComplete:function() { shuffleCheck();  },
			onMute:function(obj) { muteListener(obj); },
			onPlay:function() { playpause('ui-icon-play'); },
			onPause:function() { playpause('ui-icon-pause'); },
			onTime:function(obj) { progUpdate(obj);  },
			onIdle:function() { playpause('ui-icon-pause'); },
			onReady:function() { 
				var vol = jwplayer().getVolume();
				$('#volume').slider("value",vol); 
				$("#amount").text(vol);
			},
			onVolume:function() {
				var vol = jwplayer().getVolume();
				$('#volume').slider("value",vol); 
				$("#amount").text(vol);
			}
		}
		/* players: [
            { type: "html5" },
            { type: "flash", src: "/jwplayer/player.swf" }
        ] */
	});
				$('.delplay').live('click',function() {
					$.ajax({
						url:'view.php',
						type:'POST',
						data:({ 
							view:'delplaylist', 
							playlistid:id 
						}),
						success:function(b){ 
							$('.rtext').append(b);
						}
					});
				});
				createMoviePlayer('music','-1','375','90');
				startUI();
				
				$('#xdi').live('click',function(){
					$('#distreams').html("<img style='margin-top:75px; margin-left:100px' src='img/loading2.gif' />");
					diInfo();
					$('#distreams').dialog({
						autoOpen:true, 
						height: 400, 
						width: 350,
						modal: true, 
						resizable: false, 
						closeOnEscape: true,
						zIndex:400 
					});
				});
				$('input:#rvideo').live('click',function(){  //video radio button
					$('#vidplayercont').html("<div id='vidplayer'></div>");
					$('#bottombox').remove();
					$('#playercont').html('').hide();
					$('#vidplayercont').show();
					$('#albuminfo').hide();
					$('#nowplaying').hide();
					$('#playlist').hide();
					$('#streams').hide();
					$('#chat').hide();
					$('#livelook').hide();
					createMoviePlayer('movie','15','640','440');
					$('#playercont').html("<div id='player'></div>");
					$('#nowplayingvideo').show();
					fetchList('video');
				});
				$('input:#rmusic').live('click',function(){ //music radio button
					$('#playercont').show()
					$('#vidplayercont').html('').hide();
					$('#nowplayingvideo').hide();
					$('#buttonbox').show();
					$('#albuminfo').show();
					$('#nowplaying').show();
					$('#streams').show();
					$('#chat').show();
					$('#livelook').show();
					createMoviePlayer('music','-1','375','90');
					$('#buttonbox').position({ 
						my:'left top', 
						at:'left bottom', 
						of:'#nowplaying', 
						offset:'0 5'
					});
					fetchList('music'); 
				});
				$('#AJ').live('click',function() {
				
					var list = [];
					list[0] = { 
						file:'http://freespeech.ic.llnwd.net/stream/freespeech_thealexjonesshow32k',
						title:'Alex Jones',	
						provider:'sound'
					};
				
				
					streamInfo("http://freespeech.ic.llnwd.net/stream/freespeech_thealexjonesshow32k");
					streamStart(list);
					$('#nowplaying').html("<img src='img/aj.png' /><div id='nowplayingtext'><div id='np_band'>Alex Jones - </div><div id='track_name'>Radio Show</div><div id='bottombox'></div></div>");
				});
				$('#buttons').buttonset();
				$('#buttons button').each(function(i){ 
					$(this).css('width','55px'); 
				});
				$('#buttons .streamimgset').eq(0).css('background',"url('img/world.png')");
				$('#buttons .streamimgset').eq(1).css('background',"url('img/world.png')");
				$('#searchbutton').button({ 
					text:false,
					icons:{ primary:'ui-icon-search' }
				
				});
				$('#searchbutton').css('height','28px').css('width','28px');
				
				$('#searchbutton').position({ my:'right top',at:'right top',of:'#searchbartext', offset:'-3 0',collision:'none' });
				$('#searchbutton').position({ my:'right top',at:'right top',of:'#searchbartext', offset:'0 1',collision:'none' });
				$('#logout').live("click",function(){ 
					window.location = 'index.php?logout=1'; 
				});
				var firstrun = true
				$('#menu').buttonset();
				$('#menu .headerimgset').eq(0).css("background","url('img/0.png')");
				$('#menu .headerimgset').eq(1).css("background","url('img/1.png')");
				$('#menu .headerimgset').eq(2).css("background","url('img/2.png')");
				$('#menu .headerimgset').eq(3).css("background","url('img/3.png')");
				$('#menu .headerimgset').eq(4).css("background","url('img/4.png')");
				$('#menu .headerimgset').eq(5).css("background","url('img/5.png')");
				$('#menu').show();
				$('#cback').button({ 
					text:false,
					icons: { 
						primary: 'ui-icon-seek-start' 
					} 
				});
				$('#cplaypause').button({ 
					text:false, 
					icons: { 
						primary: 'ui-icon-play' 
					} 
				}).live('click',function(){ 
					jwplayer().play();  
				});
				$('#cstop').button({ 
					text:false, 
					icons: { 
						primary: 'ui-icon-stop'
					} 
				}).click(function() { 
					jwplayer().stop();	
				});
				$('#cnext').button({ 
					text:false, 
					icons: { 
						primary: 'ui-icon-seek-end'
					}	
				}).click(function() { 
					shuffleCheck(); 
				});
				$('#mute').button({ 
					text:false, 
					icons: { 
					primary: 'ui-icon-volume-on' 
					} 
				}).click(function() { 
					jwplayer().setMute(); 
				});
				$("#amount").text($("#volume").slider("value"));
				$('#menu .menutext').eq(3).css('marginLeft','-2px');
				$('#menu .menutext').eq(4).css('marginLeft','-3px');
				$('#menu .menutext').eq(2).css('marginLeft','-3px');
				$('#controls').show();
				$('#clearpl').button({ 
					text:false, 
					icons:{ 
						primary:'ui-icon-trash'
					}
				});
				$('#loadplaylist .removepl').live('click',function(){ 
					var id=$(this).attr('pid');
					$.ajax({ 
						url:'view.php',
						type:'POST',
						data:({ 
							playlistid:id, 
							view:'delplaylist' 
						}),
						success:function(a){ 
							$('#plstatus').text(a); 
							$(this).parent().remove();
						}
					});
				});
				$('#savepl').button({ 
					text:false, 
					icons:{ 
						primary:'ui-icon-disk'
					} 
				}).click(function() {
					$('#saveplaylist').dialog({ 
						autoOpen:true, 
						height:200, 
						width:250,
						modal:true, 
						resizable:false, 
						closeOnEscape:true, 
						zIndex:10  
					}); 
					$(this).removeClass('ui-state-focus');
				});	
				$('#createpl').live('click',function(){
					a=$('#plname:input').val();
					b='songId='+$('#pl .newtrack').eq(0).attr('songid');
					x=1;
					while (x<$('.newtrack').size()) {
						b=b+'&songId='+$('#pl .newtrack').eq(x).attr('songid');
						x++;
					}
					$.ajax({
						url:'view.php',
						type:'POST',
						data:({ 
							name:a, 
							view:'addtoplaylist', 	
							songid:b 
						}),	
						success:function(a) { 
							$('#saveplaylist .status').text('Playlist saved with '+x+' songs total.');
						}
					});
				});
				$('#loadpl').button({ 
					text:false, 
					icons:{
						primary:'ui-icon-folder-open'
					}	
				}).click(function() {
					$('#plstatus').text();
					$(this).removeClass('ui-state-focus');
					$("button", ".close").button();
					$("#loadplaylist").dialog({ 
						autoOpen:true, 
						height: 250, 
						width: 240,	
						modal: true, 
						resizable: false, 
						closeOnEscape: true,	
						zIndex:400, 
						dialogClass:"load" 
					});
					ajaxFunction('bla',"playlists");
				});	
				$('#playlistfunc').show();
				$('#done').button();
				$('#saveconfig').button();
				$('#done').click(function(){ 
					$('#search').dialog('destroy'); 
				});
				$('#plbut').click(function(){ 
					$('#playlist').show(); 
					$('#albuminfo').hide(); 
				});
				$('#config').click(function(){
					$('#configopts').buttonset();
					$('#timestamp').button();	
					if($('#search').is(':visible')) { 
						$('#search').dialog('destroy'); 
					}
					$('#configd').dialog({ 
						autoOpen:true, 
						closeOnEscape:true, 
						height:'auto', 
						width:'170px', 
						show:'drop', 
						hide:'drop', 
						position:[252,61], 
						resizable:false 
					});
				});
				$("#saveconfig").click(function(){ 
					var $shuf;
					var $reap;
					var $time;
					if ($('#shuffle:checkbox').attr('checked')) $shuf = "1";
					else $shuf = "0";
					if ($('#repeat:checkbox').attr('checked')) $reap = "1";
					else $reap = "0";
					if ($('#timestamp:checkbox').attr('checked')) $time = "1";
					else $time = "0";
					$.ajax({
						url: "view.php",
						global: false,
						type: "POST",
						data: ({ view:'settings',
							user:username,
							shuffle:$shuf, 
							repeat1:$reap, 
							timestamp:$time 
						})
					});
					$('#configd').dialog('destroy'); 
				});
				$('#chatsub').submit(function() {
					var bla = $('#cmessage').val()
					var reg = /<([A-Z][A-Z0-9]*)\b[^>]*>/i;
					var bla1 = bla.search(reg);
					if (bla1 != -1) { 
						$('#cmessage').val(""); 
						return false; 
					}
					else {
						var tmp = urlencode(bla);
						ajaxFunction('asdf','chatsend',tmp);
						oneShot();
						$('#cmessage').val("");
						return false;
					}
					return false;
				}); 
				setTimeout("doit()",2000);
				$('.prem').live("click",function(){	
					$(this).parent().remove();	
					return false; 
				});
				$('.dl').live("click",function(	){
					var a = $(this).parent().attr('songid');	
					var b=juke+'rest/download.view?'+creds+'&id=a';
					window.location = b;
					return false;
				});
				$('.padd').live("click",function(event){  //add a track to the playlist, invoked by play now or by clicking +
					$('#albuminfo').hide()
					event.preventDefault();
					$('#playlist').show();
					if ($(this).hasClass('search')) {
						var bg=juke+"rest/getCoverArt.view?"+creds+"&size=115&id="+$(this).attr('cover');
					}
					else var bg=$('#cover').find('img').attr('src');
					if ($('#rvideo:input').is(':checked')) {
						$('#playlist').position({ my:'top left', at:'bottom left', of:'#playercont', offset:'300 675' });
						$('#pl').append("<div class='newtrack video' songid=" +$(this).attr('songid')+ " time=" +$(this).attr('time')+ " duration=" + $(this).attr('duration') + " ><img src="+bg+ " /><div class='grip ui-icon ui-icon-grip-dotted-vertical'></div><div class='tholder'><div class='band'>"+$(this).attr('artist')+"</div><div class='tracktext'>"+$(this).attr('track')+"</div></div><div class='duration'>"+$(this).attr('time')+"</div></div>");
					} else { 
						$('#pl').append("<div class='newtrack' songid=" +$(this).attr('songid')+ " time=" +$(this).attr('time')+ " duration=" + $(this).attr('duration') + " ><img src="+bg+ " /><div class='grip ui-icon ui-icon-grip-dotted-vertical'></div><div class='tholder'><div class='band'>"+$(this).attr('artist')+"</div><div class='tracktext'>"+$(this).attr('track')+"</div></div><div class='duration'>"+$(this).attr('time')+"</div></div>");
					}
					totalTime();
					$('#pl .newtrack:last').fadeIn(1000);
					playlistStripe();
				});
				$('#pl .newtrack').live("click",function(){ //clicking a track in the playlist, invoked by play now
					if($(this).is('.stream')) { //stream?
						var list = []; 
						list[0] = {
							title:$(this).attr('track'),
							file:$(this).attr('url'),
							provider:"sound"
						};
						list.length = 1;
						$('*').removeClass("nowplaying");
						$('#nowplaying').html("<img src="+$(this).find('img').attr('src')+" /><div id='nowplayingtext'><div id='np_band'>"+$(this).attr('artist')+" - </div></b><div id='track_name'>"+$(this).attr('track')+"</div><div id='bottombox'></div></div></div>");			
						streamInfo($(this).attr('url'));
						$(this).addClass('nowplaying');
						jwplayer().load(list).play();
						
					}
					else { 
						var list = [];
						list[0] = {
							file:juke+"rest/stream.view?"+creds+"&id="+$(this).attr('songid'),
							duration:$(this).attr('duration'),
							provider:"sound"
						};
						
						id = $(this).attr('songid');
						list.length = 1;
						var song = $(this).find('.band').text()+' - '+$(this).find('.tracktext').text();				
						if($(this).hasClass('video')) {  //is it video?
							list[0].provider = "video";
							$('#nowplayingvideo').html("<div id='bottombox'><div id='slider'></div><div class='np_pos'></div><div class='np_dur'>"+$(this).attr('time')+"</div></div>");			
							$('#playlist').hide();					
							$('#bottombox').position({ my:'left top', at:'left top', of:'#nowplayingvideo', offset:'5 40'});
						} else { 
							$('#nowplaying').html("<img src="+$(this).find('img').attr('src')+" /><div id='nowplayingtext' songid="+id+"><div id='np_band'>"+$(this).find('.band').text()+" - </div></b><div id='track_name'>"+$(this).find('.tracktext').text()+"</div><div id='bottombox'><div id='slider'></div><div class='np_pos'></div><div class='np_dur'>"+$(this).attr('time')+"</div></div></div>");
						}
						$('*').removeClass("nowplaying");
						$("#slider").slider({
							animate:'true', 
							min:'0', 
							max:parseInt($(this).attr('duration')), 
							slide:function(event,ui) { 
								jwplayer().pause();
								jwplayer().seek(ui.value);
							}, 
							value: 0
						});
						
						$(this).addClass('nowplaying');
						jwplayer().load(list).play();
						
					}
				});
				$('#livetext .playnow').live('click',function(){ 
					$(this).prev().click(); 
					$('.newtrack:last').click(); 
				});
				$('#livetext .ladd').live('click',function(){
					if ($(this).hasClass('stream')) {
						$('#pl').append("<div class='newtrack stream' artist='"+$(this).attr('artist')+"' track='"+$(this).attr('track')+"' url=" +$(this).attr('songid')+ " time='~' duration='~' ><img src="+$(this).attr('cover')+ " /><div class='grip ui-icon ui-icon-grip-dotted-vertical'></div><div class='tholder'><div class='band'>"+$(this).attr('artist')+"</div><div class='tracktext'>"+$(this).attr('track')+"</div></div><div class='duration'>"+$(this).attr('time')+"</div></div>");
					}
					else { $('#pl').append("<div class='newtrack' songid=" +$(this).attr('songid')+ " time=" +$(this).attr('time')+ " duration=" + $(this).attr('duration') + " ><img src="+$(this).attr('cover')+ " /><div class='grip ui-icon ui-icon-grip-dotted-vertical'></div><div class='tholder'><div class='band'>"+$(this).attr('artist')+"</div><div class='tracktext'>"+$(this).attr('track')+"</div></div><div class='duration'>"+$(this).attr('time')+"</div></div>"); }
					totalTime();
					$('.newtrack:last').fadeIn(1000);
					playlistStripe();
				});
				$('.streamgenre').live("click",function(){	//di.fm stream clicked
					$('#clearpl').click();
					$.ajax({
						url:"view.php",
						dataType:'html',
						data:({ 
							view:'loadstream', 
							pic:'img/di.gif', 
							genre:$(this).find('a').attr('title'),
							url:$(this).find('a').attr('pls') 
					}),
						success: function(a){ 
							$('#pl').append(a);
							$('#pl .newtrack stream:odd').addClass("odd");
							$('#pl .newtrack stream:even').addClass("even"); 
							$('#pl .newtrack:first').click(); 
						}
					});
					$('#distreams').dialog('destroy');
					$('#albuminfo').hide()
					$('#playlist').show();
				});
				$('#results .playnow').live("click",function(i){  //search result - play now clicked
					$(this).prev().prev().click();
					$('#albuminfo').hide();
					$('#playlist').show();
					setTimeout("$('#pl .newtrack:last').click()",200);
				});
				$('#leftinner .playnow').live("click",function(i){	//main album list - play now clicked
					$(this).prev().prev().click();
					$('#albuminfo').hide();
					$('#playlist').show();
					var bg=$('#cover').find('img').attr('src');
					setTimeout("$('#pl .newtrack:last').click()",200);
				});
				$('#leftinner .ttext').live('dblclick',function(e){
					$(this).prev().prev().click();
				});
				$('#leftinner .clk,#leftinner .inneralbum a,#leftinner .album').live("contextmenu",function(e){ // right click menu for album listing
					$('#leftinner .album').removeClass('tmp');
					$(this).addClass('tmp');
					$('#rtclka').css({ top: e.pageY+'px',left: e.pageX+'px' }).show();
					
					if (!$(this).hasClass('clk')) { var b = $(this).children('.clk').attr('albumid'); $('#rtdla').attr('href',juke+"rest/download.view?"+creds+"&id="+b); }
					else $('#rtdla').attr('href',juke+"rest/download.view?"+creds+"&id="+$(this).attr('albumid'));
					return false;
				});
				$('#rtqueuea').live('click',function() {
				var a = $('#leftinner .tmp');
				
					if ($(a).parent().hasClass('inneralbum')) {
						$(a).find('.addinner').click();
					}
					
					else if ($(a).hasClass('clk')) {
						
						$(a).prev().click();
					}
					else {
						$(a).children('.addfolder').click();
					}

				});
				
				$('#pl .newtrack').live("contextmenu", function(e) { //right click menu for playlist - download/remove
					$('#pl .newtrack').removeClass('tmp');
					$(this).addClass('tmp');
					$('#rtclk').css({ top: e.pageY+'px',left: e.pageX+'px' }).show();
					$('#rtdl').attr('href',juke+"rest/download.view?"+creds+"&id="+$(this).attr('songid'));
					$('#rtrm').live('click',function() { 
						$('#pl .tmp').remove(); 
					});
					return false;
				});
				$('#rtmv').live('click',function() {
					$('#pl').sortable("destroy");
					a = "<div style='display: block;'  class='newtrack'>"+$('#pl .tmp').html()+"</div>";
					b = parseInt(prompt('Move to track number:')) - 1;
					$('#pl .newtrack').eq(b).before(a);
					$('#pl .tmp').remove();
					playlistStripe();
				});		
				$('#rtdl').click(function() { 
					$('#rtclk').hide(); 
					$('#pl .newtrack').removeClass('tmp');
				});
				$(document).click(function() { 
					$('#pl .newtrack').removeClass('tmp');
					$('#leftinner .clk').removeClass('tmp');
					$('#rtclk,#rtclka').hide(); 
				});
				$('#leftinner .album').live("click",function(e){  //loading album tracks/closing current album
					if (!$(this).hasClass('inneralbum')) {
					if($('#leftinner .album.active').length == 0) { 
						$("#leftinner").scrollTo($(this),400,{easing:'swing'});
					}
					if ($(this).hasClass('active')) { 
						$(this).next().html(''); 
						$(this).removeClass('active').removeClass('ui-state-active').next().removeClass('active'); 
						return false; 
					}
					$('#leftinner .actrack.active').hide(300,function() {
						active = $('#leftinner .album.active');
						$("#leftinner").scrollTo(active,400,{easing:'swing'});
					});
					$(this).addClass('ui-state-active');
					$('#leftinner .actrack.active').html('');
					$('#leftinner .album.active').removeClass('active').toggleClass('ui-state-active');
					$('#leftinner .actrack.active').removeClass('active');
					$(this).addClass('active').next().addClass('active');
					$('#cover').remove();
					}
					$('.preload').show();
					if($(this).parent().parent().parent().parent().attr('id') == "left") { //root folder?
						ajaxFunction($(this).find('a').eq(1).attr("albumid"),"album","bla");
					} 
					var level = $(this).attr('level');
					var status = parseInt(level)+1;
					$('#leftinner .inneralbum[level='+level+']').next().html('');
					$('#leftinner .active.inneralbum').removeClass('active').next().removeClass('active');
					if($(this).hasClass('inneralbum')) { 
						$(this).addClass('active');
						ajaxFunction($(this).find('a').eq(1).attr("albumid"),"album",$(this).find('a').eq(1).attr("inner"),level);
					}
					else if($(this).hasClass('inner')) { 
						$(this).addClass('active');
						ajaxFunction($(this).attr("albumid"),"album",$(this).attr("inner"),level);
					}
					return false;
				});
				$('#leftinner .album').live('hover',function() { 
					if (!$(this).hasClass('inneralbum')) {
						$(this).toggleClass('ui-state-hover'); 
					}
				});
				$('#leftinner .addinner').live('click',function(e){
					album = $(this).next().text();
					id = $(this).attr("albumid");
					viewsConnect('addfolder',id,album);
					$('#albuminfo').hide();
					$('#playlist').show();
					return false;
				});
				$('#leftinner .addfolder').live("click",function(event){ //adding entire album(s)
					$('#albuminfo').hide();
					$('#playlist').show();
					active = $(this).closest('.album');
					id = active.find('.clk').attr('albumid');
					bla = active.find('.clk').attr('album');
					viewsConnect('addfolder',id,bla);
					return false;
				});
				$("button", ".close").click(function() { 
					$('#loadplaylist').dialog('destroy'); 
				});
				$("#clearpl").click(function() { 
					$('#pl').html("") 
				});
				$('#sbutton').click(function() {
					if($('#configd').is(':visible')) {
						$('#configd').dialog('destroy');	
					}
					$('#search').dialog({ 
						autoOpen:true, 
						closeOnEscape:true, 
						height:'auto', 
						width:'470px', 
						show:'drop', 
						hide:'drop', 
						position:[322,67], 
						resizable:false 
					});
				});	
				$('#searchnow').button({
					text:false,
					icons:{
					primary:'ui-icon-search' 
					}
				});
				$('#searchbutton').click(function() {
					if($('#configd').is(':visible')) {
						$('#configd').dialog('destroy');	
					}
					a = 'any';
					//a = $('#newsearch input:checked').attr('value');
					$('#search').dialog({ 
						autoOpen:true, 
						closeOnEscape:true, 
						height:'auto', 
						width:'470px', 
						show:'drop', 
						hide:'drop', 
						position:[322,67], 
						resizable:false 
					});
					var keyword = $('#searchbartext').val();
					
					subsearch(keyword,a);
					$('#term').val(keyword);
					$('#research input[value='+a+']').attr('checked','checked');
				});
				$('#research').submit(function(){ 
					$('#searchnow').click(); 
					return false; 
				});
				$('#newsearch').submit(function(){ 
					$('#searchbutton').click(); 
					return false; 
				});
				$('#searchnow').live('click',function() {
					var keyword = $('#term').val();
					a = $('#research input:checked').attr('value');
					subsearch(keyword,a);
				});
				$('#search .spage').live('click',function(){
					var keyword = $('#term').val();
					a = $('#research input:checked').attr('value');
					subsearch(keyword,a,'null',$(this).attr('offset'));
				});
				$('.loadplaylist').live("click",function(){	
					$('#loadplaylist').dialog('close'); 
					$('#playlist').show(); 
					$('#albuminfo').hide(); 
					ajaxFunction($(this).attr('pid'),"loadplaylist","bla"); 
					$('#pl').html("<img style='margin-left:130px; margin-top:95px;' src='img/loading1.gif' ><br><span style='margin-left:190px;'>loading</span>"); 
					$('loadplaylist').dialog('destroy'); 
				});
				$.ajax({  //loading user preferences from server 
					url: "view.php",
					type: "GET",
					data: ({ 
						view:'prefs', 
						username:username
					}),
					dataType: "text",
					success: function(msg){
						var bla = new Array();
						msg = trim(msg);
						bla = msg.split(',');
						if (bla[0] == '1') {
							$('#cshuffle:input').attr('checked',true);
							$('#shuffle:input').attr('checked',true);
						}
						if (bla[1] == '1') {
							$("#crepeat:checkbox").attr('checked',true);
							$("#repeat:checkbox").attr('checked',true);
						}
						if (bla[2] == '1') {
							$("#ctimestamp:checkbox").attr('checked',true);
							$("#timestamp:checkbox").attr('checked',true);
						}
						$('#cplaylist').buttonset();
						$('#crepeat').button({ 
							icons: { 
								primary: 'ui-icon-refresh'
							} 
						});
						$('#cshuffle').button({
							icons: { 
								primary: 'ui-icon-shuffle' 
							} 
						});
					}
				});
				$('button').live('click',function(){ 
					$(this).removeClass('ui-state-focus');
				});
				liveCheck();
				var mylist = $('#leftinner ul');
				var listitems = mylist.children('li').get();
				listitems.sort(function(a, b) {
					var compA = $(a).text().toUpperCase();
					var compB = $(b).text().toUpperCase();
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
				});
				$.each(listitems, function(idx, itm) { 
					mylist.append(itm); 
				});
				fetchList('music'); 
	});
			<!-- 
			function ajaxFunction(a,b,c,d){
				var ajaxRequest;  // The variable that makes Ajax possible!		
					try{ // Opera 8.0+, Firefox, Safari
						ajaxRequest = new XMLHttpRequest();
					} catch (e){ // Internet Explorer Browsers
						try{
							ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
						} catch (e) {
							try{
								ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
							} catch (e){ // Something went wrong
								return false;
							}	
						}
					}	
					// Create a function that will receive data sent from the server
					ajaxRequest.onreadystatechange = function(){
						if(ajaxRequest.readyState == 4){	
							if(b=="album") {
								if(c!="bla") {
									$("#leftinner .active.album[level="+d+"]").next().html(ajaxRequest.responseText);
								}
								else {
									$('#leftinner .actrack.active').html(ajaxRequest.responseText);
									$('#leftinner .album_track:odd').addClass("odd");
									$('#leftinner .album_track:even').addClass("even");
									$('#leftinner .actrack.active').animate({ opacity:'show' },1000,'linear');
								}
								$('.preload').hide();
							}
							if(b=="chat") { $('.text').html(ajaxRequest.responseText+'</br>'); }
							if(b=="chatsend") {
								if ($("#timestamp:checkbox").attr('checked') == true) ajaxRequest.open("GET","view.php?view=chat&timestamp=true", true);
								else ajaxRequest.open("GET","view.php?view=chat", true);
							}
							if(b=="playlists") { 
								$('.playlists').html(ajaxRequest.responseText);
							}
							if(b=="loadplaylist") {
								$('#pl').html(ajaxRequest.responseText);
								playlistStripe();
								totalTime();				
							}
						}
					}
				if(b=="album") {
					if (c=='bla') {
						ajaxRequest.open("GET", "view.php?view=album&level=0&id=" + a, true);
					}
					else {
						ajaxRequest.open("GET", "view.php?view=album&level="+d+"&id=" + a, true);
					}
				}
				if(b=="chat") {
				if ($("#timestamp:checkbox").attr('checked') == true) { 
					ajaxRequest.open("GET","view.php?view=chat&timestamp=true", true); 
				}
				else ajaxRequest.open("GET","view.php?view=chat", true);
			}
			if(b=="chatsend") ajaxRequest.open("GET","view.php?view=chatsend&message="+c, true);
			if(b=="playlists") ajaxRequest.open("GET","view.php?view=playlists",true); 
			if(b=="loadplaylist") ajaxRequest.open("GET","view.php?view=loadplaylist&id="+a,true);
			ajaxRequest.send(null); 	
		}
		//-->
		