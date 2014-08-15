<?php 
include 'adminHeader.php';
$commod = new commod();
?>

<div class='rightbar'>
  <h1>Add a commodity</h1>
  <form class="vertical async-form"
  action='view/admin/action.php?action=addCommod'
  page='admin/commodities'>
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

<div class="center">
  <h1>Commodity Editor</h1>
  <?php $commods = $commod->getCommods();
  echo tableHeader(array('ID','Name','Tech','Baseprice','Type','Spawn','Delete'));
  foreach($commods as $commod) {
    echo "<tr>";
    $spawnbtn = "<a class='btn admin-action'";
    $spawnbtn.= "href='commodities'";
    $spawnbtn.= "action='spamCommod&commod=".$commod->id."'>Spawn</a>";
    $deletebtn = "<a class='btn btn-danger admin-action'";
    $deletebtn.= "href='commodities'";
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

    echo tableCells(array($commod->id,$commod->name,$commod->techlevel,$commod->baseprice,$type,$spawnbtn,$deletebtn));
    echo "</tr>";
  }

  echo tableFooter();
  ?>

</div>

