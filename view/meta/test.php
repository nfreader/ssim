<?php

require_once('../../inc/config.php');

$pilot = new pilot(NULL,TRUE);
$commod = new commod();
var_dump($commod->getSpobCommods(4));
?>
