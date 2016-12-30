<?php
include '../../inc/config.php';
$pilot = new pilot();
$outfit = new outfit();
$spob = new spob($pilot->spob);

$outfits = $outfit->getOutfitListing($spob->techlevel,$spob->govt->id);

//I know this is bad, but we need to loop through vessel and pilot outfits
//And if certain flags are set, remove the outfit from the listing below

//First we need to merge the pilot and vessel outfit arrays
$pilot->outfits = array_merge($pilot->outfits, $pilot->vessel->outfits);

//And then remove anything from the array that 1) the pilot has and
//2) can't be purchased more than once (or shouldn't be visible at all)
$remove = array();
foreach($pilot->outfits as $po) {
  if ('U' == $po->flag || 'S' == $po->flag){
    $remove[] = $po->id;
  }
}
?>

<div id="left">
  <ul class="options">
    <li><a class="page" href="home">Back</a></li>
  </ul>
</div>

<div id="center">
<h1>Outfitter - <?php echo $spob->fullname;?></h1>

<?php foreach ($outfits as $outfit) : ?>
  <?php if (!in_array($outfit->id,$remove)): ?>
    <?php echo outfitFormatter($outfit,'buy');?>
  <?php endif; ?>
<?php endforeach; ?>

<h1>Sell</h1>
<?php foreach ($pilot->outfits as $outfit) : ?>
  <?php if ('U' != $outfit->flag): ?>
    <?php echo outfitFormatter($outfit,'sell');?>
  <?php endif; ?>
<?php endforeach; ?>
</div>

<?php require_once('../rightbar.php'); ?>
