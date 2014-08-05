<?php

include '../inc/config.php';
$user  = new user();
$pilot = new pilot();
$syst = new syst($pilot->pilot->syst);
$spob = new spob($pilot->pilot->spob);

?>

<div class="leftbar">
<h1>Test Page</h1>
<ul class="options">
<li><a href='home' class='page'>Back</a></li>
</ul>
</div>

<div class="center">
<?php 
$stations = $spob->generateStation(10);
foreach($stations as $station) {
  echo $station['name'] ."<br>";
  echo $station['desc'] ." (Techlevel: ".$station['techlevel'].")<br><br>";
}
echo "<hr>";

$planets = $spob->generatePlanets(10);
foreach ($planets as $planet) {
  echo $planet['name'] ."<br>";
  echo $planet['desc'] ." (Techlevel: ".$planet['techlevel'].")<br><br>";
}

?>
</div>

<?php
include 'rightbar.php';