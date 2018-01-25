<?php
// This file is the place to store all basic functions

function is_logged_in($username = false, $password = false) {
	//if the $username, $password, session username or password, has not been set login is false
	if ((!$username || !$password) && (!isset($_SESSION['username']) || !isset($_SESSION['password']))) {
		return false;
	}
	else {                 
		// if the ping.view returns ok als value the credentials are correct
		$strFile = JUKE . 'rest/ping.view?u=' . (!$username ? $_SESSION['username'] : $username) . '&p=enc:' . (!$password ? $_SESSION['password'] : $password) . '&v=1.2.0&c=myapp';
		$arrContent = my_xml2array($strFile);
		if ($arrContent[0]['attributes']['status'] == 'ok') {
			return true;
		}
		else {                                    
			return false;
		}
		
	}
}

function is_standalone() {
	//if (DB_TYPE == "jetty") return false;
	//else { return true; }
	if (strrpos($_SERVER['SERVER_SOFTWARE'], "Apache PHP Quercus") !== false) { //great chance being a jetty dbase, still not known if it is the subsonic server
		return false;
	}
	else { 
		return true; 
	}
}

//function for returning hexed string
function str2Hex($string){
    $hex='';
    for ($i=0; $i < strlen($string); $i++) {
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}

function my_xml2array($__url) {
	$xml_values = array();
	$contents = file_get_contents($__url);
	$contents = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>","",$contents);
	
	$parser = xml_parser_create('');
	if(!$parser)
	return false;
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);
	if (!$xml_values)
	return array();
	$xml_array = array();
	$last_tag_ar =& $xml_array;
	$parents = array();
	$last_counter_in_tag = array(1=>0);
	foreach ($xml_values as $data)
	{
		switch($data['type'])
		{
		case 'open':
			$last_counter_in_tag[$data['level']+1] = 0;
			$new_tag = array('name' => $data['tag']);
			if(isset($data['attributes']))
			$new_tag['attributes'] = $data['attributes'];
			if(isset($data['value']) && trim($data['value']))
			$new_tag['value'] = trim($data['value']);
			$last_tag_ar[$last_counter_in_tag[$data['level']]] = $new_tag;
			$parents[$data['level']] =& $last_tag_ar;
			$last_tag_ar =& $last_tag_ar[$last_counter_in_tag[$data['level']]++];
			break;
		case 'complete':
			$new_tag = array('name' => $data['tag']);
			if(isset($data['attributes']))
			$new_tag['attributes'] = $data['attributes'];
			if(isset($data['value']) && trim($data['value']))
			$new_tag['value'] = trim($data['value']);
			$last_count = count($last_tag_ar)-1;
			$last_tag_ar[$last_counter_in_tag[$data['level']]++] = $new_tag;
			break;
		case 'close':
			$last_tag_ar =& $parents[$data['level']];
			break;
		default:
			break;
		};
	}
	return $xml_array;
}

function get_value_by_path($__xml_tree, $__tag_path) {
	$tmp_arr =& $__xml_tree;
	$tag_path = explode('/', $__tag_path);
	foreach($tag_path as $tag_name)
	{
		$res = false;
		foreach($tmp_arr as $key => $node)
		{
			if(is_int($key) && $node['name'] == $tag_name)
			{
				$tmp_arr = $node;
				$res = true;
				break;
			}
		}
		if(!$res)
		return false;
	}
	return $tmp_arr;
}

function mysql_prep( $value ) {
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists( "mysql_real_escape_string" ); // i.e. PHP >= v4.3.0
	if( $new_enough_php ) { // PHP v4.3.0 or higher
		// undo any magic quote effects so mysql_real_escape_string can do the work
		if( $magic_quotes_active ) { $value = stripslashes( $value ); }
		
	} else { // before PHP v4.3.0
		// if magic quotes aren't already on then add slashes manually
		if( !$magic_quotes_active ) { $value = addslashes( $value ); }
		// if magic quotes are active, then the slashes already exist
	}
	return $value;
}
function redirect_to( $location = NULL ) {
	if ($location != NULL) {
		header("Location: {$location}");
		exit;
	}
}
function confirm_query($result_set) {
	if (!$result_set) {
		die("Database query failed: " . mysql_error());
	}
}
function check_required_fields($required_array) {
	$field_errors = array();
	foreach($required_array as $fieldname) {
		if (!isset($_POST[$fieldname]) || (empty($_POST[$fieldname]) && $_POST[$fieldname] != 0)) { 
			$field_errors[] = $fieldname; 
		}
	}
	return $field_errors;
}
function check_max_field_lengths($field_length_array) {
	$field_errors = array();
	foreach($field_length_array as $fieldname => $maxlength ) {
		if (strlen(trim($_POST[$fieldname])) > $maxlength) { $field_errors[] = $fieldname; }
	}
	return $field_errors;
}

function display_errors($error_array) {
	echo "<p class=\"errors\">";
	echo "Please review the following fields:<br />";
	foreach($error_array as $error) {
		echo " - " . $error . "<br />";
	}
	echo "</p>";
}
function folderSelect($type) {
	/**
	* PURPOSE:	Grabs the folder ID's for the type of media sent in the parameter, music or video.
	* PARAMS:	$type - 'music' or 'video'
	*/
	
		$filename = getcwd().'/folders.cfg';
		$folders = unserialize(file_get_contents($filename)); 
		
		$i=0;
		foreach ($folders as $row) {
			if ($type == 'all') {
				$result[$i]['id'] = $i;
				
			}
			if($row == $type) {
				$result[$i]['id'] = $i;
				
				
			}
			$i++;
		}
		
		return $result;
	
}
?>