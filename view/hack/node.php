<?php
require_once('../../inc/config.php');
$pilot = new pilot();
$syst = new syst($pilot->syst);
?>

<div id="left">

</div>

<div id="center">

  <h1><?php echo $syst->name;?> Bluenet Node</h1>
  <span id="fingerprint"><?php echo $syst->fingerprint;?></div>

</div>
