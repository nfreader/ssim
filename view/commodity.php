<?php

include '../inc/config.php';
$user  = new user();
$pilot = new pilot();
?>

<div class="fiftyfifty">
<h1>Commodities on <?php echo $pilot->pilot->planet;?></h1>

<?php $commodities = new commod();
$commods = $commodities->getSpobCommodData($pilot->pilot->spob);
if(!$commods) {
  echo "<div class='pull-center'>&#x0226A; No commodities &#x0226B;</div>";
} else {
  foreach($commods as $purchase) {
    echo "<div class='commod purchase'>";
    echo "<form action='view/action.php?action=buyCommod&commod=$purchase->id'
    class='form async-form' page='commodity'>";
    echo "<input type='number' min='1' placeholder='Tons'";
    echo "max='".$pilot->pilot->capacity."'";
    echo "data-supply=".$purchase->supply." name='amount' />";
    echo "<button disabled>Enter amount</button></form>";
    echo "<h3>$purchase->name <small>".floor($purchase->price);
    echo icon('certificate','credits')." per ton</small>";
    echo "<small>$purchase->supply tons available</small></h3>";
    echo "</div>";
  }
}

?>

<ul class="options">
<li><a href='home' class='page'>Back</a></li>
</ul>
</div>

<div class="fiftyfifty">
<h1>Commodities in cargo hold</h1>
<?php

$cargo = $commodities->getPilotCommods($pilot->pilot->spob);
if(!$cargo) {
  echo "<div class='pull-center'>&#x0226A; No commodity cargo &#x0226B;</div>";
} else {
  foreach ($cargo as $sell) {
    echo "<div class='commod sell'>";
    echo "<form action='view/action.php?action=sellCommod&commod=$sell->id'
    class='form async-form' page='commodity'>";
    echo "<input type='number' min='1' placeholder='Tons'";
    echo "max='".$sell->amount."'";
    echo "data-supply=".$sell->amount." name='amount' />";
    echo "<button disabled>Enter amount</button></form>";
    echo "<h3>$sell->name <small>".floor($sell->price);
    echo icon('certificate','credits')." per ton</small>";
    echo "<small>$sell->amount tons in hold</small></h3>";
    if ($sell->is_legal == 0 && $pilot->pilot->syst == $sell->lastsyst) {
      echo icon('legal')."Selling this cargo here will result in a legal penalty!";
    }
    echo "</div>";
  }
}

$misn = new misn();
$pirates = $misn->getPirateableMissions();

echo tableHeader(array('Commodity',
  'Tons','Reward','Pirate'),'misn sort');

  foreach ($pirates as $deliver) {
    echo "<tr>";
    echo tableCell($deliver->commodity);
    echo tableCell($deliver->amount);
    echo tableCell($deliver->reward);
    //echo tableCell($deliver->uid);
    echo tableCell("<a class='btn local-action'
      action='pirateMission&UID=$deliver->uid' href='commodity'>Pirate</a>");
    echo "</tr>";
  }
  echo tableFooter();

?>

<p class='disclaimer'>Any attempts to artificially inflate the commodity market by 
<strong>reselling a commodity less than a week after you purchased it in the 
same system</strong> will result in a legal penalty, under the 
Interstellar Commerce Treaty (I.C.T.) § 7-12-89.</p>

<p class='disclaimer'>Reneging on mission contracts and selling cargo as a commodity will result in a legal penalty, under the 
Interstellar Commerce Treaty (I.C.T.) § 7-13-89.</p>


</div>

<?php
  include 'rightbar.php';
?>

<script>
$('.commod.purchase form input').keyup(function(){
  var value = parseFloat($(this).val());
  var max = parseFloat($(this).attr('max'));
  var supply = parseFloat($(this).attr('data-supply'));
  var btn = $(this).next('button');
  if (value > max) {
    $(btn).prop({
      disabled: true
    }).text('Order too large!');
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
  } else {
    $(btn).prop({
      disabled: false
    }).text('Purchase '+ value +' tons');
  }
})
$('.commod.sell form input').keyup(function(){
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
  } else {
    $(btn).prop({
      disabled: false
    }).text('Sell '+ value +' tons');
  }
})
</script>