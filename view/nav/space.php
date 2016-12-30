<?php $syst = new syst($pilot->syst);
consoledump($syst);?>

<div id="left">
  <div class="location-box">
    <h1><?php echo $syst->name; ?></h1>
    <small id='fingerprint'>
      In orbit
      <span class="pull-right">
      <?php if ($pilot->flags->canHack): ?>
        Node <a href='hack/node' class="page">
          <?php echo $syst->fingerprint;?>
        </a>
      <?php else : ?>
          Node <?php echo $syst->fingerprint;?>
      <?php endif; ?>
      </span>
    </small>
  </div>
    <h2 class="module">Government</h2>
      <a href="government/govt" data="govtid=<?php echo $syst->govt->id;?>" class="page">
        <?php echo $syst->govt->smallBadge;?>
      </a>
  <h2 class="module">
    Autolander <div class="right">
    <?php if ($pilot->flags->canLand && !empty($syst->spobs)): ?>
      <span class="label green">ONLINE</span>
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
    <h2 class="module">Beacon Control</h2>
    <ul class="options">
      <li><a href="nav/newBeacon" class="page">Launch Beacon
      (<?php echo singular($pilot->vessel->beacons->rounds,'beacon','beacons');?> remaining)</a></li>
    </ul>
  <?php endif;?>
</div>

<div id="center">

<h1>Bluespace Navigation <div class="right">
<?php if ($pilot->flags->canJump): ?>
  <span class="label green">ONLINE</span>
<?php else: ?>
  <span class="label red">OFFLINE</span>
<?php endif; ?>
  </div>
</h1>

<ul class="options">
<?php foreach ($syst->connections as $jump): ?>
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
      <?php endif;?> ~<?php echo $jump->distance;?> sAU
      <?php if ($jump->beacons) : ?>
        <div class="right">
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
<h2 class="module">System Message Beacons</h2>
<?php endif; ?>

<?php foreach($syst->beacons as $beacon): ?>
  <?php echo $beacon->html;?>
<?php endforeach;?>

</div>
