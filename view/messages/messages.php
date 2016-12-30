<?php 
require_once('../../inc/config.php');
$pilot = new pilot();
?>

<div id="left">
  <ul class="options">
    <li><a class="page" href="home">Home</a></li>
    <li><a class="page" href="messages/messages">Inbox</a></li>
    <li><a class="page" href="messages/messages" data="outbox">Outbox</a></li>
    <li><a class="page" href="messages/messages" data="newMsg">New Message</a></li>
  </ul>
</div>

<div id="center">
  <?php
  if (isset($_GET['newMsg'])) {
    include('newMsg.php');
  } elseif (isset($_GET['outbox'])) {
    $messages = new message();
    $messages = $messages->getOutbox();
    include('outbox.php');
  }
  elseif (isset($_GET['convo'])) {
    $convoid = $_GET['convo'];
    $messages = new message($convoid);
    include('thread.php');
  } else {
    $convoid = NULL;
    $messages = new message($convoid);
    include('inbox.php');
  }
  ?>
</div>

<?php require_once('../rightbar.php'); ?>

<script>

$('.thread').click(function(){
  var convo = $(this).attr('data-convo');
  loadView('messages/messages',"convo="+convo);
});

$('.delete-thread').click(function(e){
  e.stopPropagation();
  var convo = $(this).attr('data-convo');
  var dest = 'messages/messages';
  $(this).parent().parent().parent().html('');
  $.ajax({
    type: "GET",
    url: "view/action.php?action=deleteThread&from="+convo,
    success: function(retval) {
      loadView(dest,"msg="+encodeURIComponent(retval));
    },
    error: function(retval) {
      $('#game').empty().html(retval);
      console.log(retval);
    }
  })

});

</script>