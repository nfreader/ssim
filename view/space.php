<div class="leftbar">
<h1>In orbit at <?php echo $syst->syst->name; ?></h1>
<ul class="options">
<?php

$spob = new spob();
$spobs = $spob->getSpobs($syst->syst->id);

foreach ($spobs as $spob) {
  echo "<li><a class='local-action' action='land&spob=".$spob->id."'>";
  echo landVerb($spob->type, 'future')." ".$spob->name."</a></li>";
}

?>
</ul>
</div>

<div class="center">
<h1>Bluespace Navigation
<?php if ($pilot->pilot->fuel == 0) {
echo '<span class="pull-right red notice" title="Insufficent fuel">OFFLINE</span>';
} else {
echo '<span class="pull-right green">ONLINE</span>';
} ?>
</h1>
<ul class="options">
<?php
$jumps = $syst->getConnections($syst->syst->id);
if ($pilot->pilot->fuel == 0) {
  foreach ($jumps as $jump) {
    echo "<li><a disabled='true'>";
    echo "Initiate bluespace jump to system ".$jump->name;
    echo "</li></a>";
  }  
} else {
  foreach ($jumps as $jump) {
    echo "<li><a class='local-action' href='home'";
    echo "action='jump&target=".$jump->id."'>";
    echo "Initiate bluespace jump to system ".$jump->name;
    echo "</li></a>";
  }  
}

//include 'galaxyMap.php';
?>

</ul>

<?php 

$beacon = new beacon();
$beacons = $beacon->getBeacons($pilot->pilot->syst);

foreach ($beacons as $beacon) {
  include 'beacon.php';
}

if ($pilot->pilot->fuel == 0) {
  echo "<p>You are out of fuel and this system is uninhabited.</p>";
  echo "<a class='btn btn-block local-action' href='home' action='distressBeacon'>Launch distress beacon</a>";
}
?>
</div>