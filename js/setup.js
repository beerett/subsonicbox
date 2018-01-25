function startOp() {
	$('#hint').animate({
	opacity:.1 },750,function(){
	stopOp();
	});
	setTimeout('startOp()',1500);
}
function stopOp(){ 
	$('#hint').animate({ opacity:1 },750);
}
function getSetup(){
		$.ajax({
			url:'setup.php',
			data:({
					basic:true
			}),
			success:function(a){
				$('#main').html(a);
				$('#p1next').button({
					icons:{ primary:'ui-icon-circle-arrow-e' }
				});
				$('#p1test').button({
					icons:{ primary:'ui-icon-wrench' }
				});
				$('#subinfo').fadeOut('slow');
				$('#sform').fadeIn('slow');
				$('#hint').position({ my:'left top',at:'left bottom',of:'#setup',offset:'0 5' }).css('width','82px');
			}
		});
		
		
}
function getFolders(){
	$('#folderc').fadeIn();
	$.ajax({
		url:'setup.php',
		data:({
				rescan:true
		}),
		success:function(a){
			$('#folders').html(a);
				$('#p2next').button({
				icons:{ primary:'ui-icon-circle-arrow-e' }
			});
		}
	});
}
$(document).ready(function(){ 
	if (setupcreds == 'setup') {
		
		getSetup();
	}
	else if (setupcreds == 'folders') {
		showFolders();
	}
	else if (setupcreds == 'profile') {
	
		$('#main').html("<div id='profile'></div>");
			getProf();
	
	}
	else showFolders();
	$('#saveprof').live('click',function() {
		$.ajax({
			url:'setup.php',
				data:({ profile:'true',create:'true',username:$('#prof_user').val(),password:$('#prof_pass').val(),shuffle:'true',repeat:'true',timestamp:'true' }),
				success:function(a){
					$('#status').html(a);
					$('#prof_user').val('');
					$('#prof_pass').val('');
					
			}
		});
	
	
	});
	$('#profsing').live('click',function() {
	
		$('#profsetup').hide();
	});
	$('#profmult').live('click',function() {
	$('#profsetup').show();
	});
	
	$('#p2finish').live('click',function(){
		var ct = $('.musicfolderid').size();
		var folders = { };
		$('.musicfolderid').each(function(i){
			if ($('.music:input').eq(i).is(':checked')) {
				folders[i] = 'music';
			}
			if ($('.video:input').eq(i).is(':checked')) { 
				folders[i] = 'video';
			}
		});
		var folderString = JSON.stringify(folders);
		$.ajax({
			url:'setup.php',
			data:({ setfolders:'true', folder:folders }),
			success:function(){
				$('#folders').fadeOut();
				$('#main').html("<div id='profile'></div>");
				$.ajax({
					url:'setup.php',
					data:({ profile:'true' }),
					success:function(a){
						if(!standalone) {
							$('#profile').html(a).css('height','200px');
							$('#status').html("<p>Single Profile: Supersonic will user whatever credentials you supplied in step one to chat, stream, etc.</p><p>Multi - Supersonic will give you the option of setting up different Subsonic users and letting the user choose which profile to load.");
							$('#profsetup').hide();
						}
						else {
							$("#main").html("Setup is now complete.");
						}
					}
				});
				
			}
		});
		
	});
	
	$('#sform .text').live('hover',function(){ 
		$('#status').text($(this).attr('info')); 
		},function(){ $('#status').text(''); 
	});

	$('#p1test').live('click',function(){
	
		$.ajax({
		url:'setup.php',
		data:({ 
					ping:'true',
					server:$('#server:input').val(),
					juke:$('#juke:input').val(),
					s_user:$('#s_user:input').val(),
					s_pass:$('#s_pass:input').val()
				}),
				success:function(a){ 
					$('#status').html(a);
					
				},
				error:function(a){ $('#main').text('There was an error.'); }
			});
		
		
	});
	$('#proftest').live('click',function(){
	
		$.ajax({
		url:'setup.php',
		data:({ 
					ping:'true',
					proftest:'true',
					s_user:$('#prof_user:input').val(),
					s_pass:$('#prof_pass:input').val()
				}),
				success:function(a){ 
					$('#status').html(a);
					
				},
				error:function(a){ $('#main').text('There was an error.'); }
			});
		
		
	});
	$('#p1next').live('click',function(){
		if ($('input:#jetty').is(':checked')) {
			$.ajax({
				url:'setup.php',
				data:({ 
					setup:'true',
					db_type:'jetty',
					server:$('#server:input').val(),
					juke:$('#juke:input').val(),
					s_user:$('#s_user:input').val(),
					s_pass:$('#s_pass:input').val()
				}),
				success:function(a){ 
					$('#status').text('Settings saved.'); 
					
					$('#sform').fadeOut('slow');
					showFolders();
					
					
				},
				error:function(a){ $('#main').text('There was an error.'); }
			});
		}
		else {
			
				$.ajax({
					url:'setup.php',
					data:({ 
						setup:'true',
						db_type:'standalone',
						server:$('#server:input').val(),
						juke:$('#juke:input').val(),
						s_user:$('#s_user:input').val(),
						s_pass:$('#s_pass:input').val()
					}),
					success:function(a){ 
						$('#status').text('Settings saved. '); 
						$('#sform').fadeOut('slow');
					},
					error:function(a){ $('#main').text('There was an error.'); }
				});
			
		}
	
		$('#status').text('Please choose the correct media type for the above folders');
		
	});
	
	setTimeout('startOp()',1500);
	
	
	

				$('#menu button').each(function(){
					$(this).button();
				});
	
	$('#setup').click(function(){
		getSetup();
	});

	
		
});

function getProf(){
$.ajax({
				url:'setup.php',
				data:({ profile:'true' }),
				success:function(a){
					
					$('#profile').html(a).css('height','200px');
					$('#finishsetup').button();
					$('#status').html("<h2>Single Profile</h2> Supersonic will user whatever credentials you supplied in step one to chat, stream, etc.<h2>Multi</h2> Supersonic will give you the option of setting up different Subsonic users and letting the user choose which profile to load.");
					$('#hint').position({ my:'left top',at:'left bottom',of:'#profiles',offset:'0 5' }).css('width','64px');
					$('#finishsetup').position({ my:'right bottom', at:'right bottom', of:'#profsetup', offset:'0 30',collision:'none' });
					$('#profsetup').hide();
					$('#saveprof').button();
					$('#proftest').button();
				}
			});
}
function showFolders() {
	$.ajax({
		url:'setup.php',
		data:({ rescan:'true' }),
		success:function(a){
			$('#main').html("<div id='folders'></div>");
			$('#folders').html(a);
			$('#folders').fadeIn('slow');
			$('#p2back').button();
			$('#p2finish').button();
			$('#status').text('Please select the appropriate folder types');
			$('#hint').position({ my:'left top',at:'left bottom',of:'#foldersetup',offset:'0 5' }).css('width','93px');
			displayFolders();
		}
	});
}

function displayFolders() {

	$.ajax({ 
		url:'setup.php',
		data:({ config:'true',getfolders:'true' }),
		success:function(a){ 
			a = trim(a);
			var response = new Array();
			response = a.split(' ');
			$('#folders *:input').each(function(i) {
			   $(this).attr('checked','');
				if (response[i] == 'true') { $(this).attr('checked','checked'); }
			});
		}
	});
}
function trim(string) { return string.replace(/^\s\s*/, '').replace(/\s\s*$/, '');  }
