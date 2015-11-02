<?php 

require_once('../../inc/config.php');

var_dump($syst = new syst(1,TRUE));
var_dump($pilot = new pilot(NULL,TRUE));
$msg = new message;
$send = $msg->newSystemMessage('40abc4d3a95','Badmins','EAT A DICK');
var_dump($msg=$msg->getPilotThreads());
?>
