<?php 
include 'adminHeader.php';
$commod = new commod();
?>

<div class="center wide">
  <h1>Commodity Stats</h1>
  <?php $commods = $commod->getCommodAvgs();
  echo tableHeader(array('ID',
    'Name',
    'Spobs',
    'Baseprice',
    'Techlevel',
    'Total Supply',
    'Average Supply',
    'Average Price',
    'Spawn',
    'Delete'
    ));
  foreach($commods as $commod) {
    echo "<tr>";
    $spawnbtn = "<a class='btn admin-action'";
    $spawnbtn.= "href='commod-stats'";
    $spawnbtn.= "action='spamCommod&commod=".$commod->id."'>Spawn</a>";
    $deletebtn = "<a class='btn btn-danger admin-action'";
    $deletebtn.= "href='commod-stats'";
    $deletebtn.= "action='disableCommod&commod=".$commod->id."'>Delete</a>";

    switch($commod->class) {
      case 'R':
      $type = 'Regular';
      break;

      case 'S':
      $type = 'Special';
      $spawnbtn = '';
      break;

      case 'M':
      $type = 'Mission';
      $spawnbtn = '';
      break;
    }

    echo tableCells(array($commod->id,$commod->name,$commod->spobs,
      $commod->baseprice, $commod->techlevel,$commod->totalsupply,
      floor($commod->avgsupply),floor($commod->price)." ".
      icon('certificate','credits'),$spawnbtn,$deletebtn));
    echo "</tr>";
  }

  echo tableFooter();
  ?>

    <h1>Add a commodity</h1>
  <form class="vertical async-form"
  action='view/admin/action.php?action=addCommod'
  page='admin/commod-stats'>
    <input type='text' name='name' placeholder='Name' />
    <input type='number' name='techlevel' min='0' max='10' placeholder='Techlevel' />
    <input type='number' name='baseprice' placeholder='Baseprice' />
    <h3>Type</h3>
        <label class="radio"><input type='radio' name='type' value='R' />
          Regular
        </label>
        <label class="radio"><input type='radio' name='type' value='S' />
          Special/Specific
        </label>
        <label class="radio"><input type='radio' name='type' value='M' />
          Mission Cargo
        </label>
    <button>Add</button>
  </form>

</div>

