<?php $spob = new spob($pilot->spob); ?>

<div id="left">
  <div class="location-box">
    <h1><?php echo spobType($spob->type)." ".$spob->name;?>
      <div class="right"><?php echo $spob->techlevel; ?></div>
    </h1>
    <span id='fingerprint'>In the <?php echo $spob->parent->name;?> system</span>
    
    <img src="assets/img/planets/earth.png"
    alt="Earth" height="128" width="128" class="planet" />
    <p><?php echo $spob->description;?></p>
    
    <ul class="options">
      <li><a class="page" href='ship/shipyard'>Shipyard</a></li>
    </ul>
  </div>
</div>
