<h1>Inbox – <?php echo $pilot->name;?></h1>
  <table class="table" >
    <thead>
      <tr>
        <th>From</th>
        <th>When</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!$messages->inbox) : ?>
      <td colspan="2">
        <div class="pull-center">&#x0226A; No messages &#x0226B;</div>
      </td>
    <?php endif;?>
      <?php foreach ($messages->inbox as $thread): ?>
        <?php 0 == $thread->msgfrom ? $thread->msgfrom = "sys" : $thread->msgfrom; ?>
      <?php if (0 < $thread->unread) :?>
        <tr class="unread thread" data-convo="<?php echo $thread->msgfrom;?>">
      <?php else : ?>
        <tr class="thread" data-convo="<?php echo $thread->msgfrom;?>">
      <?php endif; ?>
        <td><?php echo $thread->sender; ?></td>
        <td>Sent <?php echo timestamp($thread->timestamp); ?>
          <div class="right">
            <i class="fa fa-minus-circle delete-thread red"
            data-convo="<?php echo $thread->msgfrom;?>"></i>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>