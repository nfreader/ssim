<div class="rightbar">
    <h1><?php echo $pilot->pilot->name;?></h1>
    <span id='fingerprint'>Fingerprint: <?php echo $pilot->fingerprint;?></span>
    <ul class="dot-leader">
      <li id='govt'>
        <span class="left">Government</span>
        <span class="right"><?php echo $pilot->pilot->government;?></span>
      </li>
      <li>
        <span class="left">Status</span>
        <span class="right"><?php
        switch($pilot->pilot->status) {
          case 'L':
          echo landVerb($spob->spob->type, 'past')." ".$spob->spob->name;
          break;

          case 'S':
          echo "In orbit at ".$syst->syst->name;
          break;
        }
        ?></span>
      </li>
      <li id='credits'>
        <span class="left">Credits</span>
        <span class="right"><?php echo $pilot->pilot->credits.icon('certificate','credits');?></span>
      </li>
      <li class="legal">
        <span class="left">Legal</span>
        <span class="right"><?php echo $pilot->pilot->legal.icon('flag');?></span>
      </li>
      <li id='ship'>
        <span class="left">Ship</span>
        <span class="right"><?php echo $pilot->pilot->vessel; ?></span>
      </li>
      <li id='make'>
        <span class="left">Make</span>
        <span class="right"><?php echo $pilot->pilot->shipname; ?></span>
      </li>
      <li id='class'>
        <span class="left">Class</span>
        <span class="right"><?php echo shipClass($pilot->pilot->class)['class']; ?></span>
      </li>
    </ul>
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
    </ul>
</div>
