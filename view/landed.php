<div class="leftbar">
  <div class="location-box">
    <h1><?php echo spobType($spob->spob->type)." ".$spob->spob->name;?></h1>
    <span id='fingerprint'>Bluespace node: <?php echo $spob->nodeid;?></span>
    <img src="assets/img/planets/earth.png"
    alt="Earth" height="128" width="128" class="planet" />
    <p><?php echo $spob->spob->description;?></p>
    
    <ul class="options">
      <?php
      echo ($pilot->pilot->fuel < $pilot->pilot->fueltank?"<li><a class='local-action' action='refuel' href='home'>Refuel</a></li>":"<li><a disabled='true'>Refuel</a></li>");

      echo ($pilot->pilot->armordam > 0 ?"<li><a class='page' href='repair'>Hull repair</a></li>":"<li><a disabled='true'>Hull repair</a></li>");
      ?>

      <li><a class='page' href='mission'>Cargo Missions</a></li>
      <li><a class='page' href='commodity'>Commodity Center</a></li>
      <li><a>Spaceport Bar</a></li>
      <li><a>Shipyard</a></li>
      <li><a>Outfitter</a></li>
    </ul>
  </div>
</div>

<div class="center">
  <?php
  echo '<h1>Departure Clearance';
  echo '<span class="green pull-right">GRANTED</span>';
  echo '</h1>';
  echo "<a class='btn btn-block local-action' ";
  echo "action='liftoff' href='home'>";
  echo landVerb($spob->spob->type, 'then')." ".$spob->spob->name."</a>";
  //include 'galaxyMap.php';
  ?>
</div>