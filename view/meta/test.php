<?php 

require_once('../../inc/config.php');

$pilot = new pilot();
$vessel = new vessel($pilot->vessel->id);
var_dump($vessel->getTradeInValue($vessel->id));


var_dump((20 - (20 % 7))/7);