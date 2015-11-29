<?php

$pilot = new pilot();

?>

<div class="leftbar">
  <div class="location-box">
    <h1><?php echo spobType($spob->spob->type)." ".$spob->spob->name;?></h1>
    <img src="assets/img/planets/earth.png"
    alt="Earth" height="128" width="128" class="planet" />
    <p><?php echo $spob->spob->description; ?></p>
    <small>Bluespace node: <?php echo $spob->nodeid; ?></small>
    <ul class="options">
      <?php
      echo ($pilot->pilot->fuelmeter > 1 ? "<li><a disabled='true'>Refuel</a></li>" : "<li><a class="page" href='home' data='action=refuel'>Refuel</a></li>");
      ?>
      <li><a>Missions</a></li>
      <li><a>Commodity Center</a></li>
      <li><a>Spaceport Bar</a></li>
      <li><a>Shipyard</a></li>
      <li><a>Outfitter</a></li>
    </ul>
  </div>
</div>

<div class="rightbar">
    <h1><?php echo $pilot->pilot->name;?></h1>
    <ul class="dot-leader">
      <li>
        <span class="left">Status</span>
        <span class="right"><?php echo landVerb($spob->spob->type, 'past')." ".$spob->spob->name;?></span>
      </li>
      <li>
        <span class="left">Credits</span>
        <span class="right"><?php echo $pilot->pilot->credits.icon('certificate','credits');?></span>
      </li>
      <li>
        <span class="left">Legal</span>
        <span class="right"><?php echo $pilot->pilot->legal.icon('flag');?></span>
      </li>
    </ul>
    <ul class="meters">
    <?php 
      echo "<li>".icon('dashboard')."".fuelMeter($pilot->pilot->fuel, $pilot->pilot->fueltank, $pilot->pilot->fuelmeter)."</li>";
      echo "<li>".icon('magnet')."".shieldMeter($pilot->pilot->shields)."</li>";
      echo "<li>".icon('wrench')."".armorMeter($pilot->pilot->armor)."</li>";
      echo "<li>".icon('th-large')."".cargoMeter($pilot->pilot->cargometer, $pilot->pilot->cargo, $pilot->pilot->cargobay)."</li>";
    ?>
    </ul>
    <ul class="options">
      <li><a><?php echo landVerb($spob->spob->type, 'then')." ".$spob->spob->name;?></a></li>
      <?php
      echo ($pilot->pilot->cargometer == 0 ? "<li><a disabled='true'>Jettison Cargo</a></li>" : "<li><a href=''>Jettison Cargo</a></li>");
      ?>
      <li><a>Self Destruct</a></li>
    </ul>
</div>