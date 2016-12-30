<?php
$pilot = new pilot(FALSE);
$pilots = $pilot->getUserPilots($user->uid);
$count = count($pilots);
?>

<div id="left">
<?php if ($count < 3) : ?>
  <ul class="options">
    <li>
      <a href='home' data='newPilot' class="page">
        Add new pilot <?php echo "($count/3)";?>
      </a>
    </li>
  </ul>
<?php endif; ?>
</div>
<div id="center">
  <?php
  if ((isset($_GET['newPilot'])) || (array() == $pilots)) :
    include('html/newPilot.php');
  else : ?>
    <h1>Active Pilots</h1>
    <ul class="options">
    <?php foreach ($pilots as $pilot) : ?>
      <li>
        <a class="page" href='home' data='activatePilot=<?php echo $pilot->uid;?>'><?php echo $pilot->name; ?>
        <small><?php echo $pilot->fingerprint; ?> <br> <?php echo credits($pilot->credits);?> | <?php echo $pilot->legal;?>
          </small>
        </a>
      </li>
    <?php endforeach; ?>
    </ul>
  <?php endif;  ?>

  <?php if (SSIM_DEBUG) :?>
    <p class="ooc">This game is in a development and testing phase. Data can and will be manipulated, lost or even deleted without notice.</p>
  <?php endif; ?>
</div>
