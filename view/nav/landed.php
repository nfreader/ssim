<?php $spob = new spob($pilot->spob,'parent');
consoledump($spob);?>

<div id="left">
  <div class="location-box">
    <h1><?php echo $spob->fullname; ?></h1>
    <small id='fingerprint'>
      In the <?php echo $spob->parent->name;?> system
      <div class="right">Techlevel <?php echo $spob->techlevel; ?></div>
    </small>
    <p><?php echo $spob->description;?></p>
    <h2>Government</h2>
      <a href="government/govt" data="govtid=<?php echo $spob->govt->id;?>" class="page">
      <?php echo $spob->govt->smallBadge;?>
      </a>

    <h2>Facilities</h2>
    <ul class="options">
    <?php if ($pilot->flags->canRefuel && $pilot->credits >= $spob->fuelcost) : ?>
      <li><a class='action' href='refuel' data-dest="home">Refuel</a></li>
    <?php endif; ?>
    <?php if ($spob->techlevel >= 2) : ?>
      <li><a class="page" href='commod/commod'>Commodity Exchange</a></li>
    <?php endif; ?>
    <?php if ($spob->techlevel >= 3) : ?>
      <li><a class="page" href='outfit/outfitter'>Outfitter</a></li>
    <?php endif; ?>
    <?php if ($spob->techlevel >= 5) : ?>
      <li><a class="page" href='ship/shipyard'>Shipyard</a></li>
    <?php endif; ?>
    <?php if ($spob->techlevel >= 7) : ?>
      <li><a class="page" href='misn/misnlist'>Cargo Missions</a></li>
    <?php endif; ?>
    </ul>
  </div>
</div>

<div id="center">
<h1>Autolander <div class="right">
  <?php if ($pilot->flags->canLiftoff): ?>
    <span class="green">ONLINE</span>
  <?php else: ?>
    <span class="red">OFFLINE</span>
  <?php endif; ?>
    </div>
</h1>
<ul class="options">
<?php if ($pilot->flags->canLiftoff):?>
  <li><a href="liftoff" data-dest="home" class="action color green">Liftoff</a></li>
<?php endif; ?>
</ul>
</div>
