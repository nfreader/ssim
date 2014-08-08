<?php

include '../inc/config.php';
$pilot = new pilot();
$spob = new spob($pilot->pilot->spob);
?>

<div class="leftbar">
<div class="location-box">
  <h1><?php echo spobType($spob->spob->type)." ".$spob->spob->name;?></h1>
  <span id='fingerprint'>Bluespace node: <?php echo $spob->nodeid;?></span>
</div>  
  <ul class="options">
    <li><a href='home' class='page'>Back</a></li>
  </ul>

</div>
<div class="center"><h1>Hull Repair</h1>

  <div class="bignumber">
    <div class="unit">Hull Integrity</div>
    <div class="number"><?php echo floor($pilot->pilot->armor);?>%</div>
  </div>

  <div class="invoice">
  <h2>Vessel Repair Estimate</h2>
    <ul class='dot-leader'>
      <li><span>Hull Restoration</span><span>2500cr.</span></li>
      <li><span>Structural Realignment</span><span>4000cr.</span></li>
      <li><span>Repressurization</span><span>1000cr.</span></li>
      <li><span>Atmospheric Testing</span><span>3000cr.</span></li>
      <li><span>Parts &amp; Labor</span><span>1200cr.</span></li>
      <li><span>I.C.T. Taxes &amp; Fees</span><span>200cr.</span></li>
      <li><span>Total</span><span>11900cr.</span></li>
    </ul>
    <p>Expected repair duration: 14 hours</p>
    <a class="btn btn-block">Begin repairs</a>
  </div>

</div>

<?php include 'rightbar.php'; ?>
<script>
  loadContent('ping', '.footer', '.footerbar');
</script>


