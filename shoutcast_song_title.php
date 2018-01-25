<?php
require_once('shoutcast_class.php');
$display_array = array("Stream Title", "Stream Genre", "Stream URL", "Current Song", "Server Status", "Stream Status", "Listener Peak", "Average Listen Time", "Stream Title", "Content Type", "Stream Genre", "Stream URL", "Current Song");
$server = $_GET['server'];
$radio = new Radio("$server");
$data_array = $radio->getServerInfo($display_array);
if ($server == "http://freespeech.ic.llnwd.net/stream/freespeech_thealexjonesshow32k") print_r($data_array[0].' - '.$data_array[2]);
else print_r($data_array[5].' - '.$data_array[10]);
?>
