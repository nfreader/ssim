<?php $syst = new syst($pilot->syst);
consoledump($syst);?>

<div class="leftbar">
  <div class="location-box">
    <h1><?php echo $syst->name; ?>
      <div class="pull-right"></div>
    </h1>
    <span id='fingerprint'>
      In orbit
      <?php if ($pilot->flags->canHack): ?>
      <div class="pull-right">
        Node <a href='hack/node' class="page">
          <?php echo $syst->fingerprint;?>
        </a>
      </div>
      <?php else : ?>
        <div class="pull-right">
          Node <?php echo $syst->fingerprint;?>
        </div>
      <?php endif; ?>
    </span>
  </div>
    <h2>Government</h2>
      <a href="government/govt" data="govtid=<?php echo $syst->govt->id;?>" class="page label pull-center"
  style="background: <?php echo $syst->govt->color1;?>; color: <?php echo $syst->govt->color2;?>;">
        <?php echo $syst->govt->name;?>
      </a>
  <h2>
    Autolander <div class="pull-right">
    <?php if ($pilot->flags->canLand && !empty($syst->spobs)): ?>
      <span class="green">ONLINE</span>
    <?php else: ?>
      <span class="red">OFFLINE</span>
    <?php endif; ?>
      </div>
  </h2>
  <?php if (empty($syst->spobs)) :?>
    <div class="pull-center">&#x0226A; No valid locations &#x0226B;</div>
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

  <?php if ($pilot->vessel->beacons):?>
    <h2>Beacon Control</h2>
    <ul class="options">
      <li><a href="nav/newBeacon" class="page">Launch Beacon
      (<?php echo singular($pilot->vessel->beacons->rounds,'beacon','beacons');?> remaining)</a></li>
    </ul>
  <?php endif;?>
</div>

<div class="center">

<h1>Bluespace Navigation <div class="pull-right">
<?php if ($pilot->flags->canJump): ?>
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
    <?php if (!$pilot->flags->canJump):?> disabled <?php endif; ?>
    >
      Jump to System <?php echo $jump->name; ?>,
      <?php if ($jump->ports):?>
        <?php echo singular($jump->ports,'port','ports');?>.
      <?php else : ?>
        (Unpopulated)
      <?php endif;?> ~<?php echo $distance;?> sAU
      <?php if ($jump->beacons) : ?>
        <div class="pull-right">
          <i class="fa fa-circle-o red panic-icon" title="Distress beacon detected"></i>
        </div>
      <?php endif; ?>
    </a>
  </li>
<?php endforeach; ?>

<?php if (!$pilot->flags->canJump && !$pilot->flags->canRefuel && empty($syst->spobs)) : ?>
  <a href="distressBeacon" data-dest="home" class="action btn btn-block color orange">
    &#x0226A; Launch distress beacon &#x0226B;
  </a>
<?php endif; ?>
</ul>

<?php if ($syst->beacons) : ?>
<h2>Message Beacons</h2>
<?php endif; ?>

<?php foreach($syst->beacons as $beacon): ?>
<?php $type = beaconTypes($beacon->type); ?>
<div class="beacon <?php echo $beacon->class; ?>">
  <?php if (TRUE == $beacon->targetable): ?>
  <div class="pull-right">
    <a href="#" data-dest="home" class="load btn color red" disabled>
      Destroy
    </a>
  </div>
  <?php endif; ?>
  <?php echo $beacon->header; ?>
  <?php echo "<p id='beacon-<?php echo $beacon->id'>$beacon->content</p>";?>
  <?php echo $beacon->footer; ?>
</div>
<?php endforeach;?>

</div>
