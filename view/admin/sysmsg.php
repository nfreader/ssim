<?php 
include 'adminHeader.php';

?>

<div class="center wide">
  <h1>Send System Message</h1>
  <?php
  $pilot = new pilot(false);
  echo "<form action='view/admin/action.php?action=sendSysMsg'
  class='form async-form vertical' page='admin/sysmsg'>";
  echo $pilot->getPilotSelectList();
  echo "<input type='text' name='from' placeholder='From' />";
  echo "<textarea name='message' placeholder='Enter your message'></textarea>";
  echo "<button>Send</button>";
  echo "</form>";

  ?>
</div>
