<?php $spob = new spob($pilot->spob); ?>

<div class="leftbar">
  <div class="location-box">
    <h1><?php echo $spob->fullname; ?></h1>
    <span id='fingerprint'>
      In the <?php echo $spob->parent->name;?> system
      <div class="pull-right">Techlevel <?php echo $spob->techlevel; ?></div>
    </span>
    
    <img src="assets/img/planets/earth.png"
    alt="Earth" height="128" width="128" class="planet" />
    <p><?php echo $spob->description;?></p>
    <ul class="options">
    <?php if ($pilot->canRefuel && $pilot->credits >= $spob->fuelcost) : ?>
      <li><a class='action' href='refuel' data-dest="home">Refuel</a></li>
    <?php endif; ?>
    <?php if ($spob->techlevel >= 3) : ?>
      <li><a class='load' href='outfit/outfits'>Outfitter</a></li>
    <?php endif; ?>
    <?php if ($spob->techlevel >= 5) : ?>
      <li><a class='load' href='ship/shipyard'>Shipyard</a></li>
    <?php endif; ?>
    <?php if ($spob->techlevel >= 7) : ?>
      <li><a class='load' href='misn/misnlist'>Cargo Missions</a></li>
    <?php endif; ?>
    </ul>
  </div>
</div>

<div class="center">
<?php if ($pilot->canLiftoff):?> 
  <a href="liftoff" data-dest="home" class="action btn btn-block">Liftoff</a>
<?php endif; ?>
</div>
