<div class="rightbar">
    <h1><?php echo $pilot->pilot->name;?></h1>
    <span id='fingerprint'>Fingerprint <?php echo $pilot->fingerprint;?></span>
    <?php

    switch($pilot->pilot->status) {
      case 'L':
      $status = landVerb($pilot->pilot->spobtype, 'past')."
       ".$pilot->pilot->planet;
      break;

      case 'S':
      $status = "In orbit at ".$pilot->pilot->system;
      break;
    }

    echo optionList(array(
      'Government'=>$pilot->pilot->government,
      'Status'=> $status,
      'Credits' => credits($pilot->pilot->credits),
      'Legal' => $pilot->pilot->legal.icon('flag'),
      'Ship' => $pilot->pilot->vessel,
      'Make' => $pilot->pilot->shipname,
      'Class' => shipClass($pilot->pilot->class)['class']
    )); ?>
  
    <ul class="meters">
    <?php 
      echo "<li>".icon('dashboard')."".fuelMeter($pilot->pilot->fuel, $pilot->pilot->fueltank, $pilot->pilot->fuelmeter)."</li>";
      echo "<li>".icon('magnet')."".shieldMeter($pilot->pilot->shields)."</li>";
      echo "<li>".icon('wrench')."".armorMeter($pilot->pilot->armor)."</li>";
      echo "<li>".icon('th-large')."".cargoMeter($pilot->pilot->cargometer, $pilot->pilot->cargo, $pilot->pilot->cargobay)."</li>";
    ?>
    </ul>
    <ul class="options">
      <?php
      echo ($pilot->pilot->cargometer == 0 ? "<li><a disabled='true'>Jettison Cargo</a></li>" : "<li><a href=''>Jettison Cargo</a></li>");
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
