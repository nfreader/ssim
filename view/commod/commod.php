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
  <h2>I.C.T. Notice</h2>
  <p class='disclaimer'>Any attempts to artificially inflate the commodity market by
  <strong>reselling a commodity less than a week after you purchased it in the
  same system</strong> will result in a legal penalty, under the
  Interstellar Commerce Treaty (I.C.T.) § 7-12-89.</p>
</div>

<div id="center">
<h1><?php echo $spob->fullname;?> Commodity Exchange</h1>
<h2>For Sale</h2>
<?php foreach ($spob->commods as $commod): ?>
  <div class="commodity purchase">
    <form class="async" data-dest="commod/commod"
    action="buyCommod&commod=<?php echo $commod->id;?>">
      <input type="number" min="1" max="<?php echo $pilot->cargo->capacity; ?>"
      name="amount" placeholder="Amount"
      data-supply="<?php echo $commod->supply;?>" />
      <button disabled>Enter Amount</button>
    </form>
    <h3>
      <?php echo $commod->name;?>
      <small>
        <?php echo credits($commod->price); ?>/ton •
        <?php echo singular($commod->supply,'ton','tons');?> available
      </small>
    </h3>
  </div>
<?php endforeach; ?>

<h2>In Hold</h2>
<?php if (empty($pilot->cargo->commods)) :?>
  <div class="pull-center">&#x0226A; No commodity cargo &#x0226B;</div>
<?php endif; ?>
<?php foreach ($pilot->cargo->commods as $commod): ?>
  <div class="commodity sell">
    <form class="async" data-dest="commod/commod"
    action="sellCommod&commod=<?php echo $commod->id;?>">
      <input type="number" min="1" max="<?php echo $commod->amount; ?>"
      name="amount" placeholder="Amount"
      data-supply="<?php echo $commod->amount;?>" />
      <button disabled>Enter Amount</button>
    </form>
    <h3>
      <?php echo $commod->name;?>
      <small>
        <?php echo credits($commod->price); ?>/ton •
        <?php echo singular($commod->amount,'ton','tons');?> in hold
        <?php if (FALSE == $commod->is_legal) :?>
          <br><em>You will face a legal penalty if you sell this cargo here</em>
        <?php endif; ?>
      </small>
    </h3>
  </div>
<?php endforeach; ?>
</div>

<script>
$('.commodity.purchase form input').keyup(function(){
  var value = parseFloat($(this).val());
  var max = parseFloat($(this).attr('max'));
  var supply = parseFloat($(this).attr('data-supply'));
  var btn = $(this).next('button');
  if (value > max) {
    $(btn).prop({
      disabled: true
    }).text('Order too large!').addClass('color red').removeClass('color green');
  } else if (value > supply) {
    $(btn).prop({
      disabled: true
    }).text('Not enough supply!');
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
    }).text('Purchase '+ value +' ton').addClass('color green').removeClass('color red');
  } else {
    $(btn).prop({
      disabled: false
    }).text('Purchase '+ value +' tons').addClass('color green').removeClass('color red');
  }
})
$('.commodity.sell form input').keyup(function(){
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
    }).text('Sell '+ value +' ton');
  } else {
    $(btn).prop({
      disabled: false
    }).text('Sell '+ value +' tons');
  }
})
</script>
