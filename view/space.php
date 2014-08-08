<div class="leftbar">
<h1>In orbit at <?php echo $syst->syst->name; ?></h1>
<ul class="options">
<?php
if ($syst->uninhabited === true) {
  echo "<div class='pull-center'>&#x0226A; System Uninhabited &#x0226B;</div>";
} else {
  $spob = new spob();
  $spobs = $spob->getSpobs($syst->syst->id);
  foreach ($spobs as $spob) {
    echo "<li><a class='local-action' href='home' action='land&spob=".$spob->id." '>";
    echo landVerb($spob->type, 'future')." ".$spob->name."</a></li>";
  }
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

<?php $targets = $pilot->getSystPilots();
echo "<div class='contacts'>"; 
echo '<h1>System Scan <span class="pull-right green">';
if (!$targets) {
  echo "NO CONTACT";
} else {
  echo "CONTACT";
}
echo "</span></h1>";
  echo "<div class='scanresults'>";
  if(!$targets) {
    echo "<div class='pull-center'>&#x0226A; No contact &#x0226B;</div>";
  } else {
  echo "<script>$.playSound('assets/sound/interface/powerUp2');</script>";
    foreach ($targets as $target) {
      include 'html/contact.php';
    }
  
  }
  echo "</div>";

echo "</div>";
?>
<h1>Beacon Control</h1>
<?php 

$beacon = new beacon();
$beacons = $beacon->getBeacons($pilot->pilot->syst);

foreach ($beacons as $beacon) {
  include 'html/beacon.php';
}

if ($pilot->pilot->fuel == 0 && $syst->uninhabited === true) {
  echo "<p>You are out of fuel and this system is uninhabited.</p>";
  echo "<a class='btn btn-block local-action' href='home' action='distressBeacon'>Launch distress beacon</a>";
}
?>
</div>