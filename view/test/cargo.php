<?php 
include '../../inc/config.php';
?>

<div class='fiftyfifty'>
<?php

$pilot = new pilot(false); 
var_dump($pilot->getPilotCargoStats(8));

$pilot = new pilot(true,true); 
var_dump($pilot->getPilotCargoStats());

var_dump($_SESSION);

?>
</div>

<div class='fiftyfifty'>

</div>