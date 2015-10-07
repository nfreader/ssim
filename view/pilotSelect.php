<?php 
$pilot = new pilot();
$pilots = $pilot->getUserPilots($user->uid); 
$count = count($pilots);
?>

<div class="leftbar">
<?php if ($count < 3) : ?>
  <ul class="options">
    <li>
      <a href='home' data='newPilot' class='load'>
        Add new pilot <?php echo "($count/3)";?>
      </a>
    </li>
  </ul>
<?php endif; ?>
</div>
<div class="center">
  <?php
  if ((isset($_GET['newPilot'])) || (array() == $pilots)) :
    include('html/newPilot.php');
  else : ?>
    <h1>Active Pilots</h1>
    <ul class="options">
    <?php foreach ($pilots as $pilot) : ?>
      <li>
        <a class='load' href='home' data='activatePilot=<?php echo $pilot->uid;?>'><?php echo $pilot->name; ?>
        <small><?php echo $pilot->fingerprint; ?> <br> <?php echo credits($pilot->credits);?> | <?php echo $pilot->legal;?>
          </small>
        </a>
      </li>
    <?php endforeach; ?>
    </ul>
  <?php endif;  ?>
</div>