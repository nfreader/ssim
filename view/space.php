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
<h1>Bluespace Navigation <span class="pull-right green">ONLINE</span></h1>
<ul class="options">
<?php
$jumps = $syst->getConnections($syst->syst->id);
foreach ($jumps as $jump) {
  echo "<li><a class='local-action' action='jump&target=".$jump->id."'>";
  echo "Initiate bluespace jump to system ".$jump->name;
  echo "</li></a>";
}
?>
</ul>
</div>