<?php
include '../../inc/config.php';
$pilot = new pilot();
$outfit = new outfit();
$spob = new spob($pilot->spob);

$outfits = $outfit->getOutfitListing($spob->techlevel,$spob->govt->id);

//I know this is bad, but we need to loop through vessel and pilot outfits
//And if certain flags are set, remove the outfit from the listing below

$remove = array();

foreach($pilot->vessel->outfits as $vo) {
  if ('U' == $vo->flag || 'S' == $vo->flag){
    $remove[] = $vo->id;
  }
}
foreach($pilot->outfits as $po) {
  if ('U' == $po->flag || 'S' == $po->flag){
    $remove[] = $po->id;
  }
}
?>

<div class="leftbar">

</div>

<div class="center">
<h1>Outfiiter - <?php echo $spob->fullname;?></h1>

<?php foreach ($outfits as $outfit) : ?>
  <?php if (in_array($outfit->id,$remove)): return; else: ?>
  <h2><?php echo $outfit->name;?>
    <div class="pull-right"><?php echo credits($outfit->cost);?></div>
  </h2>
<?php endif; ?>
<?php endforeach; ?>
</div>

<?php require_once('../rightbar.php'); ?>
