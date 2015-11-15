<?php 

require_once('../../inc/config.php');

$pilot = new pilot(NULL,TRUE);
$outfit = new outfit();
var_dump($pilot);
var_dump($outfit->buyOutfit(2));
var_dump($outfit->buyOutfit(3));
?>
