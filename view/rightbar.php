
<?php if ('F' === $pilot->status) {
  include 'freshPilot/freshRightBar.php';
  return;
} ?>

<div class="rightbar">
  <h1><?php echo $pilot->name;?></h1>
  <span id='fingerprint'>Fingerprint <?php echo $pilot->fingerprint;?></span>
  <?php
  echo optionList(array(
    'Government'=>$pilot->govt->name,
    'Status'=> $pilot->fullstatus,
    'Credits' => credits($pilot->credits),
    'Legal' => $pilot->legal.icon('flag'),
    'Ship' => $pilot->vessel->name,
    'Make' => $pilot->vessel->ship->name,
    'Class' => shipClass($pilot->vessel->ship->class)['class']
  )); ?>
  
  <ul class="meters">
    <li><?php echo $pilot->vessel->fuelGauge; ?></li>
  </ul>

  <ul class="options">
    <li><a href='galaxyMap' class='page'>Galactic Map</a></li>
    <li><a href='about' class='page'>About</a></li>
  </ul>
</div>

<?php consoleDump($pilot); ?>
