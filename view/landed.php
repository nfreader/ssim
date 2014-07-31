<div class="leftbar">
  <div class="location-box">
    <h1><?php echo spobType($spob->spob->type)." ".$spob->spob->name;?></h1>
    <img src="assets/img/planets/earth.png"
    alt="Earth" height="128" width="128" class="planet" />
    <p><?php echo $spob->spob->description;?></p>
    <small>Bluespace node: <?php echo $spob->nodeid;?></small>
    <ul class="options">
      <?php
      echo ($pilot->pilot->fuel < $pilot->pilot->fueltank?"<li><a class='local-action' action='refuel' href='home'>Refuel</a></li>":"<li><a disabled='true'>Refuel</a></li>");
      ?>
      <li><a>Missions</a></li>
      <li><a>Commodity Center</a></li>
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