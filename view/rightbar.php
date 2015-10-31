
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
    '<a href="ship/viewShip" data="ship='.$pilot->vessel->id.'" class="load">Ship</a>' => $pilot->vessel->name,
    'Make' => $pilot->vessel->ship->name,
    'Class' => shipClass($pilot->vessel->ship->class)['class']
  )); ?>
  
  <ul class="meters">
    <li><?php echo $pilot->vessel->fuelGauge; ?></li>
    <li><?php echo $pilot->vessel->shieldGauge; ?></li>
    <li><?php echo $pilot->vessel->armorGauge; ?></li>
    <li><?php echo meter("Cargo (".$pilot->cargo->cargo." / ".$pilot->cargo->cargobay.")",0,$pilot->cargo->cargometer);?></li>
  </ul>

  <ul class="options">
  <?php if (!$pilot->flags->isLanded): ?>
    <li><a href='commod/cargo' class='page'>Cargo Management</a></li>
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

 $('body').delegate('.rightbar #ship .right', 'click', function() {
    if ($(this).data('clicked')) {
      return;
    }
    var text = $(this).text();
    var form = "<input name='vesselName' id='singleField' data-action='renameVessel' placeholder='" + text + "' />";
    $(this).html(form);
    $(this).data('clicked', true);
});

 </script>