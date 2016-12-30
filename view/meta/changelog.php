<?php
include '../../inc/config.php';?>

<div id="left">
  <ul class="options">
    <li><a href='about' class='page'>Back</a></li>
  </ul>
</div>
<?php

$changes[] = array(
  'type'=>1,
  'title'=>'Beacon Launchers',
  'text'=>"Players can now purchase beacon launchers and beacons to launch. These can be used to create a message beacon in a system.",
  'date'=>'2015-12-07'
);

$changes[] = array(
  'type'=>2,
  'title'=>"Redesign",
  'text'=>"The CSS has been modified and the game should now be a little more stylish. Futher tweaking will be done.",
  'date'=>'2015-12-01'
);

$changes[] = array(
  'type'=>1,
  'title'=>"Government functions",
  'text'=>"Work has begun on making governments more funcitonal. This is one of the last things that need to be implemented before work on combat mechanics can begin",
  'date'=>'2015-12-01'
);


$changes[] = array(
  'type'=>1,
  'title'=>"Galaxy Map",
  'text'=>"The galaxy map will load (maybe) the second time you visit the galaxy map page.",
  'date'=>'2015-11-27'
);

$changes[] = array(
  'type'=>1,
  'title'=>"Ship Status View",
  'text'=>"You can now view your ship and information about it by clicking on the Ship field in the right-hand sidebar",
  'date'=>'2015-11-23'
);

$changes[] = array(
  'type'=>2,
  'title'=>"Style changes",
  'text'=>"Changes to the CSS were made to increase responsiveness",
  'date'=>'2015-11-23'
);
$changes[] = array(
  'type'=>1,
  'title'=>"Dedicated changelog page",
  'text'=>"This is the best I can do without resorting to weird database or GitHub things.",
  'date'=>'2015-11-23'
);

?>

<div id="center" class="wide"><h1>Changelog</h1>
  <ul class="options changelog">
    <?php foreach ($changes as $change) :?>
      <?php switch($change['type']) {
        case 1:
        default:
          $class="<span class='green'><i class='fa fa-plus'></i></span>";
        break;
        case 2:
          $class="<span class='orange'><i class='fa fa-pencil'></i></span>";
        break;
      } ?>
      <li>
        <h3><?php echo $class; ?> <?php echo $change['date'];?> - <?php echo $change['title'];?></h3>
        <?php echo $change['text']; ?><br>
        <span id="fingerprint"></span>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
