<?php

if (!isset($pilot)) :
  return;
else:

if ('F' === $pilot->status) {
  include 'freshPilot/freshRightBar.php';
  return;
} ?>

<div id="right">
  <h1><?php echo $pilot->name;?></h1>
  <small id='fingerprint'>Fingerprint <?php echo $pilot->fingerprint;?></small>

<ul class="dot-leader">
  <li>
    <span>Government</span>
    <span>
      <a href="government/govt" data="govtid=<?php echo $pilot->govt->id;?>" class="page label inline"
    style="background: <?php echo $pilot->govt->color1;?>; color: <?php echo $pilot->govt->color2;?>;">
      <?php echo $pilot->govt->name;?>
      </a>
    </span>
  </li>
  <li>
    <span>Status</span>
    <span><?php echo $pilot->fullstatus;?></span>
  </li>
  <li>
    <span>Credits</span>
    <span><?php echo credits($pilot->credits);?></span>
  </li>
  <li>
    <span>Legal</span>
    <span><?php echo $pilot->legal;?><i class="fa fa-flag"></i></span>
  </li>
  <li>
    <span><a href="ship/viewShip" data="ship=<?php echo $pilot->vessel->id;?>"
    class="page">Ship</a>
    </span>
    <span>
      <a id="vesselName" class="editable">
        <?php echo $pilot->vessel->name;?>
      </a>
    </span>
  </li>
  <li>
    <span>Make</span>
    <span><?php echo $pilot->vessel->ship->name;?></span>
  </li>
  <li>
    <span>Make</span>
    <span><?php echo shipClass($pilot->vessel->ship->class)['class'];?></span>
  </li>
</ul>

  <div class="meters">
  <h2 class="module">Vessel Status</h2>
  <?php echo $pilot->vessel->fuelGauge; ?>
  <?php echo $pilot->vessel->shieldGauge; ?>
  <?php echo $pilot->vessel->armorGauge; ?>
  <?php echo meter("Cargo (".$pilot->cargo->cargo." / ".$pilot->cargo->cargobay.")",0,$pilot->cargo->cargometer);?>
  </div>

  <ul class="options">
  <h2 class="module">Manage</h2>
  <?php if (!$pilot->flags->isLanded): ?>
    <li><a href='commod/cargo' class='page'>Cargo Management</a></li>
  <?php endif;?>
  <?php if (TRUE === $pilot->flags->newMessages): ?>
    <li>
      <a href='messages/messages' class='page'>
        Message Center
        <div class="left"><i class="fa fa-circle red"></i></div>
      </a>
    </li>
  <?php else: ?>
    <li><a href='messages/messages' class='page'>Message Center</a></li>
  <?php endif;?>
    <li><a href='galaxyMap' class='page'>Galactic Map</a></li>
    <li><a href='about' class='page'>About</a></li>
  </ul>
</div>

<?php
  consoledump($pilot);
  consoledump($_SESSION);
?>

<script>

$('#vesselName').editable({
    type: 'text',
    pk: '',
    url: 'view/action.php?action=renameVessel',
    title: 'Change user legal rating'
});

 </script>
 <?php endif;?>
