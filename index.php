<?php 
include(getcwd()."/includes/header.php");
define('ADMIN','1');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
	
		<title>Jukebox</title>
		
		<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
		<link type="text/css" href="css/custom-theme/jquery-ui-1.8.2.custom.css" rel="stylesheet" />	
		<script type="text/javascript" src="js/jquery-ui-1.8.2.custom.min.js"></script>
		<script type="text/javascript" src="js/colortip-1.0-jquery.js"></script>
		<script type="text/javascript" src="js/jwplayer.js"></script>
		<link type='text/css' href='css/login.css' rel='stylesheet' />
		<link rel="stylesheet" type="text/css" href="css/nopad.css" /> 
		<link rel="stylesheet" type="text/css" href="css/colortip-1.0.css"/>

		<script type="text/javascript">
		$(document).ready(function(){
			$('*').css('borderTopLeftRadius','0px');
			$('*').css('borderTopRightRadius','0px');
			$('*').css('borderBottomRightRadius','0px');
			$('*').css('borderBottomLeftRadius','0px');

			$("form").submit(function(){
				$('#lb').toggleClass('ui-state-active');
				$('#lb').attr("value","Loading..");
			});
		});

		</script>

		<script type="text/javascript" src="js/jquery.scrollTo-1.4.2-min.js"></script>
	
		
		<script type="text/javascript">
		
			function getUser(){ 
				$.ajax({
					url:'/../top.view',
					success:function(a) {
					
						var myreg = new RegExp("(?:out\\s)(.*)(?=<\/a>)");  
						var match = myreg.exec(a);
						var username = match[1];
						$.ajax({
							url:'view.php',
							type:'POST',
							data:({ profileid:username }),
							success:function(a){
								$('#profload').append("<br>"+a);
								$('#profbar').progressbar({ value:100 });
								$('#selectprof').button().css('float','left').css('marginTop','10px');
								$('#createprof').button().css('float','right').css('marginTop','10px');
								
							}
						});
					}

				});
			}
		
		var standalone = '<?php echo is_standalone(); ?>';
		var juke = '<?php echo JUKE;?>';
		var creds = '<?php echo CREDS;?>';
		var server = '<?php echo SERVER;?>';
		<?php 
		
		if (isset($_SESSION['username'])) {
			$username = $_SESSION['username'];
			echo "var username = \"$username\";\n";
		}
		?>
		
		$(document).ready(function(){
		
		
		<?php
		if (isset($_GET['clearsess'])) { session_destroy(); }
		if (ADMIN == 1) {
			echo "
			$('#tabs').tabs();
			$('#admin').live('click',function(){ 
				$('#admind').dialog({ dialogClass:'admind',modal:'true',autoOpen:'true',closeOnEscape:'true',show:'drop',hide:'drop',height:400,width:550,position:'center'    });
				$('#admind .ui-tabs-nav').removeClass('ui-widget-header');
				$('#admind *').removeClass('ui-widget-content');
			});
			$('#deleteuser').live('click',function() {
				a = $(this).prev().val();
				$.ajax({
					url:'admin.php',
					data:({ deleteuser:a  }),
					type:'GET',
					dataType:'text',
					success: function(a) {
						$('.dresponse').text(a);
					},
					error: function(a) {
						$('.dresponse').text(a);
					}
				});
			});
			
			$('#adduser').live('click',function() {
				username = $('#unadd').val();
				pw = $('#pwadd').val()
				$.ajax({
					url:'admin.php',
					data:({ username:username, password:pw  }),
					type:'POST',
					dataType:'text',
					success: function(a) {
						$('.dresponse').text(a); 
						
					}
				});
				return false; 
			});
			
			$('#clearcurrent').live('click',function(){ 
				droplive('all');  
			});";
		}
		?>
		
		});
		</script>

		
	</head>
	
<body>
<?php
	if (!$boolRunSetup) {
		if (is_standalone()) {
			if(is_logged_in() === false) {
			
				include(getcwd().'/login.php');
			}
			else {
						
				echo "<script type='text/javascript' src='js/juke.js'></script>";
				include(getcwd().'/juke.php');
			}
		}
		else if(!isset($profileid)) {
		
		
			echo "<style type='text/css'>	.ui-progressbar-value { background-image: url(img/pbar-ani.gif); } </style><script type='text/javascript'>
			$(document).ready(function(){ 
				getUser(); \n
				
			}); </script>";
		
		
			echo "<div id='profload'></div>";
		}
	}
	else {
		echo "<p style=\"color:white;\">Jukebox not <a href='setup.php'>setup</a></p>"; 
	}
	
?>

</body></html>