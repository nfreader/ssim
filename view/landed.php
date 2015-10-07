<?php $spob = new spob($pilot->spob); ?>

<div class="leftbar">
  <div class="location-box">
    <h1><?php echo spobType($spob->type)." ".$spob->name;?></h1>
    
    <img src="assets/img/planets/earth.png"
    alt="Earth" height="128" width="128" class="planet" />
    <p><?php echo $spob->description;?></p>
    
    <ul class="options">
    <?php if ('B' === $pilot->status) : ?>
      <li><a class='load' href='shipyard'>Shipyard</a></li>
    <?php else : ?>
      <?php
      echo ($pilot->fuel < $pilot->fueltank?"<li><a class='local-action' action='refuel' href='home'>Refuel</a></li>":"<li><a disabled='true'>Refuel</a></li>");

      echo ($pilot->armordam > 0 ?"<li><a class='page' href='repair'>Hull repair</a></li>":"<li><a disabled='true'>Hull repair</a></li>");
      ?>

      <li><a class='page' href='mission'>Cargo Missions</a></li>
      <li><a class='page' href='commodity'>Commodity Center</a></li>
      <li><a>Spaceport Bar</a></li>
      <li><a class='page' href='shipyard'>Shipyard</a></li>
      <li><a>Outfitter</a></li>
    <?php endif; ?>
    </ul>
  </div>
</div>

<div class="center">
<?php if ('B' === $pilot->status) :?> 
  <h1>Departure Clearance <span class="red pull-right">NEGATIVE</span></h1>
  <p class='pull-center'>&#x0226A; You can't take off without a ship! &#x0226B;</p>
<?php endif; ?>
  
</div>