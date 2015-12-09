<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw==" crossorigin="anonymous">
  </head>
  <body>
<?php
require_once('../inc/config.php');

if(isset($_GET['antag']) && isset($_GET['protag'])) {
  $antag = new pilot($_GET['antag']);
  $protag = new pilot($_GET['protag']);
} else {
  die("No pilots specified");
}

$protag->vessel = parseOutfitsModify($protag->vessel);
$antag->vessel = parseOutfitsModify($antag->vessel);

var_dump($protag->vessel);
var_dump($antag->vessel);

?>

<div class="container">
<h1><?php echo $protag->name;?>'s <em><?php echo $protag->vessel->name;?></em> (The protagonist) is attacking <?php echo $antag->name;?>'s <em><?php echo $antag->vessel->name;?></em> (The antagonist)</h1>

<h2>Stats</h2>

<table class="table">
  <thead>
    <tr>
      <th></th>
      <th>Protagonist</th>
      <th>Antagonist</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th>Ship</th>
      <td><?php echo $protag->vessel->ship->name;?></td>
      <td><?php echo $antag->vessel->ship->name;?></td>
    </tr>
    <tr>
      <th>Class</th>
      <td><?php echo $protag->vessel->ship->classname;?></td>
      <td><?php echo $antag->vessel->ship->classname;?></td>
    </tr>
    <tr>
      <th>Mass (tons)</th>
      <td><?php echo $protag->vessel->ship->mass;?></td>
      <td><?php echo $antag->vessel->ship->mass;?></td>
    </tr>
    <tr>
      <th>Accel (m/s<sup>2</sup>)</th>
      <td><?php echo $protag->vessel->ship->accel;?></td>
      <td><?php echo $antag->vessel->ship->accel;?></td>
    </tr>
    <tr>
      <th>Turn (m/s<sup>2</sup>)</th>
      <td><?php echo $protag->vessel->ship->turn;?></td>
      <td><?php echo $antag->vessel->ship->turn;?></td>
    </tr>
    <tr>
      <th>Evasion Chance</th>
      <td><?php echo $protag->vessel->ship->baseEvasion;?>%</td>
      <td><?php echo $antag->vessel->ship->baseEvasion;?>%</td>
    </tr>
    <tr>
      <th></th>
      <td colspan=2>
        Evasion chance is the chance that this vessel will have to evade an attack when they're defending
      </td>
    </tr>
    <tr>
      <th>Shields</th>
      <td><?php echo $protag->vessel->ship->shields - $protag->vessel->shielddam;?></td>
      <td><?php echo $antag->vessel->ship->shields - $antag->vessel->shielddam;?></td>
    </tr>
    <tr>
      <th>Armor</th>
      <td><?php echo $protag->vessel->ship->armor - $protag->vessel->armordam;?></td>
      <td><?php echo $antag->vessel->ship->armor - $antag->vessel->armordam;?></td>
    </tr>
    <tr>
      <th>Outfits</th>
      <td>
        <ul>
          <?php foreach($protag->vessel->outfits as $outfit):?>
            <?php echo "<li>$outfit->name ($outfit->type $outfit->subtype) (".$outfit->quantity."x)</li>";?>
          <?php endforeach; ?>
        </ul>
      </td>
      <td>
        <ul>
          <?php foreach($antag->vessel->outfits as $outfit):?>
            <?php echo "<li>$outfit->name ($outfit->type $outfit->subtype) (".$outfit->quantity."x)</li>";?>
          <?php endforeach; ?>
        </ul>
      </td>
  </tbody>
</table>

<?php

DEFINE('NUMBER_OF_TICKS',100); //How many ticks the battle can run before being declared a draw

$tick = 1;

$stats = new stdclass;
$stats->attack = 0;
$stats->defend = 0;
$stats->attacked = 0;
$stats->evaded = 0;

$protag->stats = $stats;
$antag->stats = $stats;


while($tick <= NUMBER_OF_TICKS) {
  echo "<strong>Tick: $tick</strong><br>";
  $result = battleTick($protag,$antag,$tick);
  $protag = $result->protag;
  $antag = $result->antag;
  var_dump($result->cointoss);
  var_dump($result->outcome);
  var_dump($result->fired);
  $tick++;
}

var_dump($protag->vessel);
var_dump($antag->vessel);
?>

    </div>
   </body>
 </html>
