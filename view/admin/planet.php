<?php 
include 'adminHeader.php';

$spob = new spob();
$spob = $spob->getSpob($_GET['spob']);

?>

<div class='leftbar'>
<h1>Details</h1>
<ul class="dot-leader">
  <li>
    <span class='left'>ID</span>
    <span class='right'><?php echo $spob->id; ?></span>
  </li>
  <li>
    <span class='left'>System</span>
    <span class='right'><?php echo "<a href='admin/system' query='syst=".$spob->parent."' class='load'>". $spob->system."</a>"; ?></span>
  </li>
  <li>
    <span class='left'>Type</span>
    <span class='right'><?php echo spobType($spob->type); ?></span>
  </li>
    <li>
    <span>Government</span>
    <span><?php echo $spob->govt; ?></span>
  </li>
  <li>
    <span>Tech Level</span>
    <span><?php echo $spob->techlevel; ?></span>
  </li>  
</ul>
</div>

<div class="center">
  <h1><?php echo spobType($spob->type)." ".$spob->name;?></h1>
  <p><?php echo $spob->description; ?></p>
</div>