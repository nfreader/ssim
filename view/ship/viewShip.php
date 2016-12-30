<?php
include '../../inc/config.php';
$pilot = new pilot();

?>

<div id="left">
  <ul class="options">
    <li><a href='home' class='page'>Back</a></li>
  </ul>
</div>

<div id="center">

<h1><?php echo $pilot->vessel->name;?></h1>
<span class="fingerprint">Registration number <?php echo $pilot->vessel->registration;?></span>

<ul class="meters">
  <li><?php echo $pilot->vessel->fuelGauge; ?></li>
  <li><?php echo $pilot->vessel->shieldGauge; ?></li>
  <li><?php echo $pilot->vessel->armorGauge; ?></li>
  <li><?php echo meter("Cargo (".$pilot->cargo->cargo." / ".$pilot->cargo->cargobay.")",0,$pilot->cargo->cargometer);?></li>
</ul>

<h2>Outfits</h2>
<?php foreach ($pilot->vessel->outfits as $outfit) : ?>
  <?php echo outfitFormatter($outfit);?>
<?php endforeach; ?>

<h2>Commodity cargo</h2>
<?php if (empty($pilot->cargo->commods)) :?>
  <div class="pull-center">&#x0226A; No commodity cargo &#x0226B;</div>
<?php endif; ?>
<?php foreach ($pilot->cargo->commods as $commod): ?>
  <div class="commodity jettison">
    <?php if (!$pilot->flags->isLanded) :?>
    <form class="async" data-dest="ship/viewShip"
    action="jettisonCommod&commod=<?php echo $commod->id;?>">
      <input type="number" min="1" max="<?php echo $commod->amount; ?>"
      name="amount" placeholder="Amount"
      data-supply="<?php echo $commod->amount;?>" />
      <button disabled>Enter Amount</button>
    </form>
  <?php endif;?>
    <h3>
      <?php echo $commod->name;?>
      <small>
        <?php echo singular($commod->amount,'ton','tons');?> in hold
      </small>
    </h3>
  </div>
<?php endforeach; ?>

</div>

<div id="right">

<h1><?php echo $pilot->vessel->ship->name;?>
  <div class="right"><?php echo $pilot->vessel->ship->classname;?></div>
</h1>
<span class="fingerprint"><?php echo $pilot->vessel->ship->shipwright;?></span>

<ul class="dot-leader">
  <li>
    <span>Fueltank</span>
    <span><?php echo singular($pilot->vessel->ship->fueltank,'jump','jumps');?></span>
  </li>
  <hr>
  <li>
    <span>Shields</span>
    <span><?php echo icon('magnet');?> <?php echo $pilot->vessel->ship->shields; ?></span>
  </li>
  <li>
    <span>Armor</span>
    <span><?php echo icon('th');?> <?php echo $pilot->vessel->ship->armor; ?></span>
  </li>
  <hr>
  <li>
    <span>Mass</span>
    <span><?php echo singular($pilot->vessel->ship->mass,'ton','tons');?></span>
  </li>
  <li>
    <span>Acceleration</span>
    <span><?php echo $pilot->vessel->ship->accel;?> m/s</span>
  </li>
  <li>
    <span>Turn Speed</span>
    <span><?php echo $pilot->vessel->ship->turn;?> m/s</span>
  </li>
  <li>
    <span>Acceleration</span>
    <span><?php echo $pilot->vessel->ship->accel;?> m/s</span>
  </li>
  <hr>
  <li>
    <span>Cargo Bay</span>
    <span><?php echo singular($pilot->vessel->ship->cargobay,'ton','tons');?></span>
  </li>
  <li>
    <span>Expansion Space</span>
    <span><?php echo singular($pilot->vessel->ship->expansion,'ton','tons');?></span>
  </li>
</ul>

</div>
