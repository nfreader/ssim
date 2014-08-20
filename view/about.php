<?php

include '../inc/config.php';
$pilot = new pilot();
$spob = new spob($pilot->pilot->spob);
?>

<div class="fiftyfifty">
<h1>About Spacesim</h1>  
  <ul class="options">
    <li><a href='home' class='page'>Back</a></li>
  </ul>

</div>
<?php

$changes = array(
  array(
    'class'=>'olive',
    'content'=>'Added a changelog!'
  ),



);
?>

<div class="fiftyfifty"><h1>Changelog</h1>
  <ul class="options changelog">
    <?php foreach ($changes as $change) {
      echo "<li class='color ".$change['class']."'> ".$change['content']."</li>";
    } ?>
  </ul>
</div>

<?php include 'rightbar.php'; ?>
<script>
  loadContent('ping', '.footer', '.footerbar');
</script>


