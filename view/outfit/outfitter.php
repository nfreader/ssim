<?php
include '../../inc/config.php';
$pilot = new pilot();
$spob = new spob($pilot->spob,array('outfit'));
$outfit = new outfit();

$outfits = $spob->outfits;

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
<div class="row">
  <?php $i = 1; foreach ($outfits as $outfit) : ?>
    <?php if (!in_array($outfit->id,$remove)): ?>
      <?php echo $outfit->htmlListing;
      echo ($i %2 == 0)?'</div><div class="row">':''; $i++;
      ?>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<h1>Sell</h1>
<?php foreach ($pilot->outfits as $outfit) : ?>
  <?php if ('U' != $outfit->flag): ?>
    <?php echo outfitFormatter($outfit,'sell');?>
  <?php endif; ?>
<?php endforeach; ?>

<?php require_once('../rightbar.php'); ?>
