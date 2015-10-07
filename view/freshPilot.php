<?php $spob = new spob($pilot->spob); ?>

<div class="leftbar">
  <div class="location-box">
    <h1><?php echo spobType($spob->type)." ".$spob->name;?>
      <div class="pull-right"><?php echo $spob->techlevel; ?></div>
    </h1>
    <span id='fingerprint'>In the <?php echo $spob->parent->name;?> system</span>
    
    <img src="assets/img/planets/earth.png"
    alt="Earth" height="128" width="128" class="planet" />
    <p><?php echo $spob->description;?></p>
    
    <ul class="options">
      <li><a class='load' href='shipyard'>Shipyard</a></li>
    </ul>
  </div>
</div>

<div class="center">
<p>A heavy *thump* marks the start of your journey as the bored I.C.T. official stamps your pilot's license. Years of work, days of studying and hours of standing in line have all led to this point. In the eyss of the Interstellar Commerce Treaty and its signatories, <?php echo $pilot->name;?> is now recognized as a licensed pilot. The only thing left to do now is to buy a ship and see what the galaxy has in store for you.</p>
<p><a href="shipyard" class="load">You head over to the shipyard</a></p>
</div>

<?php require_once('rightbar.php'); ?>
