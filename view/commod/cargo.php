<?php

include '../../inc/config.php';
$pilot = new pilot();
$spob = new spob($pilot->spob);

require_once('../rightbar.php');

?>

<div class="leftbar">
  <ul class="options">
    <li><a class="load" href="home">Back</a></li>
  </ul>
</div>

<div class="center">
<h1><em>BSV <?php echo $pilot->vessel->name;?></em> Cargo Hold</h1>
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
$('.commodity.jettison form input').keyup(function(){
  var value = parseFloat($(this).val());
  var max = parseFloat($(this).attr('max'));
  var supply = parseFloat($(this).attr('data-supply'));
  var btn = $(this).next('button');
  if (value > max) {
    $(btn).prop({
      disabled: true
    }).text('ERROR');
  } else if (value > supply) {
    $(btn).prop({
      disabled: true
    }).text('ERROR');    
  } else if (value < 0) {
    $(btn).prop({
      disabled: true
    }).text('Quantum Error');
  } else if (value == 0 || isNaN(value)) {
    $(btn).prop({
      disabled: true
    }).text('Enter amount');
    value = max;
  } else if (value == 1){
    $(btn).prop({
      disabled: false
    }).text('Jettison '+ value +' ton');
  } else {
    $(btn).prop({
      disabled: false
    }).text('Jettison '+ value +' tons');
  }
})
</script>