<?php $syst = new syst($pilot->syst); ?>

<div class="leftbar">
  <div class="location-box">
    <h1><?php echo $syst->name; ?>
      <div class="pull-right"><small><?php echo $syst->coords; ?></small></div>
    </h1>
    <span id='fingerprint'>
      In orbit
      <div class="pull-right">
        Node: <?php echo $syst->fingerprint;?>
      </div>
    </span>
  </div>
  <h2>Autolander</h2>
  <?php if (empty($syst->spobs)) :?>
    <div class="pull-center">&#x0226A; No valid landing sites &#x0226B;</div>
  <?php endif; ?>
  <ul class="options">
  <?php foreach($syst->spobs as $spob): ?>
    <li>
      <a href="land&spob=<?php echo $spob->id;?>" data-dest="home" class="action">
        <?php echo landVerb($spob->type,'future')." ".spobName($spob->name,$spob->type); ?>
      </a>
    </li>
  <?php endforeach; ?>
  </ul>
</div>

<div class="center">

<h1>Bluespace Navigation <div class="pull-right">
<?php if ($pilot->canJump): ?>
  <span class="green">ONLINE</span>
<?php else: ?>
  <span class="red">OFFLINE</span>
<?php endif; ?>
  </div>
</h1>

<ul class="options">
<?php foreach ($syst->connections as $jump): ?>
  <?php
  $distance = floor(abs(sqrt((($jump->coord_x - $syst->coord_x)**2)+(($jump->coord_y - $syst->coord_y)**2))));
  ?>
  <li>
    <a href="jump&target=<?php echo $jump->id; ?>" data-dest="home"
    class="action"
    <?php if (!$pilot->canJump):?> disabled <?php endif; ?>
    >
      Jump to System <?php echo $jump->name; ?>, <?php echo singular($distance,'Lightyear','Lightyears');?>
      <?php if ($jump->beacons) : ?>
        <div class="pull-right">
          <i class="fa fa-circle red panic-icon" title="Distress beacon detected"></i>
        </div>
      <?php endif; ?>
    </a>
  </li>
<?php endforeach; ?>

<?php if (!$pilot->canJump && $pilot->canRefuel && empty($syst->spobs)) : ?>
  <a href="distressBeacon" data-dest="home" class="action btn btn-block">
    &#x0226A; Launch distress beacon &#x0226B;
  </a>
<?php endif; ?>
</ul>

<?php if ($syst->beacons) : ?>
<h2>Message Beacons</h2>
<?php endif; ?>

<?php foreach($syst->beacons as $beacon): ?>
<?php $type = beaconTypes($beacon->type); ?>
<div class="beacon <?php echo $type['class']; ?>">
  <div class="pull-right">
    <a href="#" data-dest="home" class="load red btn color red" disabled>
      Destroy
    </a>
  </div>
  <?php echo $type['header'].$beacon->content;?>
  <hr>
  <small>Beacon expires <?php echo timestamp($beacon->expires);?></small>
</div>
<?php endforeach;?>

</div>
