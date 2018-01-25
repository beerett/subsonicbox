

<div style="margin:0px auto; width:500px;">
<table id="structure"  >
	<tr>
		
		<td id="page"><div class="ui-overlay"><div class="ui-widget-overlay"></div>
		<div class="ui-widget-shadow" style="position:absolute; top:200px; width:302px; height:225px;"></div></div>

				<div style="position:absolute; top:200px; height:203px; width:280px; padding: 10px;" class="ui-widget ui-widget-content">

									<h2 style="margin-top:0px; padding-bottom:10px; border-bottom:1px solid #545454;">User Login</h2>
			<?php if (!empty($message)) { echo "<p style='font-size:10px' class=\"message\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
			<form action="index.php" method="post">
			<table style="color:#fff">
				<tr>
					<td>Username:</td>
					<td><input style="width:180px" type="text" name="username" maxlength="30" value="" /></td>
				</tr>
				<tr>
					<td>Password:</td>
					<td><input style="width:180px" type="password" name="password" maxlength="30" value="" /></td>
				</tr>
				<tr>
					<td colspan="2"><input id='lb' class='ui-state-hover' style="float:right; margin-top:15px;" type="submit" name="submit" value="Login" /></td>
				</tr>
			</table>
			</form>

		
		
		</td>
	</tr>
</table>
</div>
</div>