<?php 
include 'adminHeader.php';
?>

<div class="center wide">
  <h1>Activity Log</h1>
  <?php

  echo tableHeader(array('ID','User','Pilot','Action','Data','Timestamp'));

  $game = new game();
  $logs = $game->getLogs();
  foreach ($logs as $log) {
    echo "<tr>";
    echo tableCells(array($log->id, $log->username, $log->name,
        gameLogActionTypes($log->what), $log->data, $log->timestamp));
    echo "</tr>";
  }
  echo tableFooter();
  ?>
</div>
