<?php

include '../inc/config.php';
$user  = new user();
$pilot = new pilot();
$syst = new syst($pilot->pilot->syst);
$spob = new spob($pilot->pilot->spob);
?>

<div class="leftbar">
<h1>Message Testing</h1>
<ul class="options">
<li><a href='home' class='page'>Back</a></li>
</ul>
</div>

<div class="center">
<?php

$message = new message();
$message->newPilotMessage(4,"TEST TEST TEST UR A BITCH");

?>
</div>

<?php
include 'rightbar.php';