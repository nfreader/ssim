
<?php if ('F' === $pilot->status) {
  include 'freshRightBar.php';
  return;
} ?>

<div class="rightbar">
  <h1><?php echo $pilot->name;?></h1>
  <span id='fingerprint'>Fingerprint <?php echo $pilot->fingerprint;?></span>
  <?php
  
  echo optionList(array(
    'Government'=>$pilot->government,
    'Status'=> $status,
    'Credits' => credits($pilot->credits),
    'Legal' => $pilot->legal.icon('flag'),
    'Ship' => $pilot->vessel,
    'Make' => $pilot->shipname,
    'Class' => shipClass($pilot->class)['class']
  )); ?>
  
  <ul class="meters">
  <?php 
    echo "<li>".icon('dashboard')."".fuelMeter($pilot->fuel, $pilot->fueltank, $pilot->fuelmeter)."</li>";
    echo "<li>".icon('magnet')."".shieldMeter($pilot->shields)."</li>";
    echo "<li>".icon('wrench')."".armorMeter($pilot->armor)."</li>";
    echo "<li>".icon('th-large')."".cargoMeter($pilot->cargometer, $pilot->cargo, $pilot->cargobay)."</li>";
    ?>
    </ul>
    <ul class="options">
      <?php
      echo ($pilot->cargometer == 0 ? "<li><a disabled='true'>Jettison Cargo</a></li>" : "<li><a href=''>Jettison Cargo</a></li>");
      ?>
      <li><a disabled='true'>Self Destruct</a></li>
      <li><a href='test' class='page'>Test Page</a></li>
      <?php $message = new message();
      $count = $message->getUnreadCount();
      if($count > 0) {
        echo "<li><a href='messages' class='page newmsgs'
        title='New Messages'>";
        echo icon('circle','newmsgs')."Message Center</a></li>";
      } else {
        echo "<li><a href='messages' class='page'>Message Center</a></li>";
      }

      ?>
      
      <li><a href='galaxyMap' class='page'>Galactic Map</a></li>
      <li><a href='about' class='page'>About</a></li>
    </ul>
</div>
