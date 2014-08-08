<?php

include '../inc/config.php';
$user  = new user();
$pilot = new pilot();
$syst = new syst($pilot->pilot->syst);
$spob = new spob($pilot->pilot->spob);

?>

<div class="fiftyfifty">
<h1>Commodities on <?php echo $spob->spob->name;?></h1>
<ul class="options">
<li><a href='home' class='page'>Back</a></li>
</ul>
</div>

<div class="fiftyfifty">
<h1>Commodities in cargo hold</h1>
<?php $commod = new commod();
print_r($commod->spamCommods());
?>
</div>

<?php
include 'rightbar.php';