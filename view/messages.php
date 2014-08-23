<?php

include '../inc/config.php';
$user  = new user();
$pilot = new pilot();
$syst = new syst($pilot->pilot->syst);
$spob = new spob($pilot->pilot->spob);
?>

<div class="leftbar">
<h1>Message Center</h1>
<ul class="options">
<li><a href='messages' class='page'>Inbox</a></li>
<li><a href='home' class='page'>Back</a></li>
</ul>
</div>

<div class="center">
<?php

if(isset($_GET['convo'])) {
  $messages = new message();
  $thread = $messages->getMessageThread($_GET['convo']);
  if ($_GET['convo'] == 0) {
    $sender = "Automated messages";
  } else {
    $name = $pilot->getPilotNameByID($_GET['convo']);
    $sender = "Conversation with ".$name;
  }
    echo "<h1>$sender</h1>";
  if(!$thread) {
    echo "<div class='pull-center'>&#x0226A; No messages &#x0226B;</div>";
  } else {
    foreach ($thread as $message) {
      if ($message->read == 0) {
        $class = 'unread';
      } else {
        $class = '';
      }
    
      if ($message->msgfrom == $pilot->pilot->id) {
        $class.= ' self';
      }

      echo "<div class='msg single $class'>";
      echo "<h3>$message->sender";
      if ($message->read == 0) {
        echo " ".icon('star');
      }
      echo "<small>Fingerprint: ".$message->fingerprint."</small></h3>";
      echo "<p>".nl2br($message->messagebody)."</p>";
      echo "<p><small>Sent ".relativeTime($message->timestamp);
      echo " from $message->sendnode ";
      echo "<a href='messages' class='local-action'";
      echo "action='deleteMessage&msgid=".$message->id."'>";
      echo "[delete]</a></small>";
      echo "</p>";
      echo "</div>";
      if ($message->msgfrom != $pilot->pilot->id) {
        $messages->markMessageRead($message->id);
      }
    }
  }
  if ($message->msgfrom != 0) {
    $to = $_GET['convo'];
    include 'html/msgreply.php';
  }
} else {
  $message = new message();
  $threads = $message->getPilotThreads();
  if(!$threads){
    echo "<div class='pull-center'>&#x0226A; No Messages &#x0226B;</div>";
  } else {
    //print_r($threads);
    echo tableHeader(array('',''));
    foreach($threads as $thread) {
      if ($thread->read == 0) {
        $class = 'unread';
      } else {
        $class = '';
      }
      if ($thread->system == 0) {
        $class.= ' system';
      }
      echo "<tr class='$class'";
      echo ">";
      echo tableCell("<a class='load'
        href='messages.php?convo=$thread->msgfromid'>$thread->msgfrom</a>");
      echo tableCell(relativeTime($thread->timestamp)."<span
        class='pull-right'><a class='local-action' href='messages'
        action='deleteThread&from=".$thread->msgfromid."'>
          <i class='fa fa-times-circle'></i></a>
      </span>");
      echo "</tr>";
    }
  }
}
echo tableFooter();

?>
</div>

<?php
include 'rightbar.php';
?>

<script>
$('tr.msg-load').click(function(){
  var link = $(this).attr('href');
  $('#game').empty().load('view/' + link);
})
</script>