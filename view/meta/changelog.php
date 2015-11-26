<?php
include '../../inc/config.php';?>

<div class="leftbar">
  <ul class="options">
    <li><a href='about' class='page'>Back</a></li>
  </ul>
</div>
<?php

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

<div class="center wide"><h1>Changelog</h1>
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
