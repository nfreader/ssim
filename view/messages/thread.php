<h1>Conversation with <?php echo $messages->conversation->with->name;?></h1>
<div class="conversation">
<?php foreach ($messages->conversation->posts as $post): ?>

<?php 
if (!$post->read) {
  $class = 'unread';
  if ($post->msgfrom != $pilot->uid) {
    $messages->markMessageRead($post->id);
  }
} else {
  $class = '';
  }
    
if ($post->msgfrom == $pilot->uid) {
  $class.= ' self';
} ?>

  <div class="msg single <?php echo $class;?>">
    <h3>
    <?php if (!$post->read && $post->msgfrom != $pilot->uid): ?>
      <i class="fa fa-star" title="New message"></i>
    <?php endif; ?>
    <?php echo $post->sender; ?>
      <small><?php echo $post->fingerprint;?> - <?php echo timestamp($post->timestamp);?></small>
    </h3>
    <p><?php echo nl2br($post->messagebody); ?></p>
    <p>
      <small class="tech">
        Message Traversal: <?php echo $post->sendnode;?> - % - <?php echo $post->recvnode;?>
      </small>
    </p>
  </div>
<?php endforeach; ?>
<?php if (0 != $messages->conversation->with->id):?>
<form class="async" action="sendMsg&to=<?php echo $convoid;?>"
data-dest="messages/messages">
  <h3>Reply to <?php echo $messages->conversation->with->name;?></h3>
  <textarea name="message" placeholder="Enter your message"></textarea>
  <button>Send</button>
</form>
<?php else: ?>
<div class="pull-center">&#x0226A; You cannot reply to system messages &#x0226B;</div>
<?php endif;?>
</div>