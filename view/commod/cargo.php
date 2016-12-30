<?php

include '../../inc/config.php';
$pilot = new pilot();
$spob = new spob($pilot->spob);

require_once('../rightbar.php');

?>

<div id="left">
  <ul class="options">
    <li><a class="page" href="home">Back</a></li>
  </ul>
</div>

<div id="center">
<h1><?php echo $pilot->vessel->name;?> Cargo Hold</h1>
<h2>Commodity cargo</h2>
<?php if (empty($pilot->cargo->commods)) :?>
  <div class="pull-center">&#x0226A; No commodity cargo &#x0226B;</div>
<?php endif; ?>
<?php foreach ($pilot->cargo->commods as $commod): ?>
  <div class="commodity jettison">
    <form class="async" data-dest="commod/cargo"
    action="jettisonCommod&commod=<?php echo $commod->id;?>">
      <input type="number" min="1" max="<?php echo $commod->amount; ?>"
      name="amount" placeholder="Amount"
      data-supply="<?php echo $commod->amount;?>" />
      <button disabled>Enter Amount</button>
    </form>
    <h3>
      <?php echo $commod->name;?>
      <small>
        <?php echo singular($commod->amount,'ton','tons');?> in hold
      </small>
    </h3>
  </div>
<?php endforeach; ?>
</div>

<script>

</script>
