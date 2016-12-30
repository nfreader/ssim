<h1>Outbox – <?php echo $pilot->name;?></h1>
  <table class="table" >
    <thead>
      <tr>
        <th>From</th>
        <th>When</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!$messages) : ?>
      <td colspan="2">
        <div class="pull-center">&#x0226A; No messages &#x0226B;</div>
      </td>
    <?php endif;?>
      <?php foreach ($messages as $thread): ?>
        <tr class="thread" data-convo="<?php echo $thread->msgto;?>">
        <td><?php echo $thread->name; ?></td>
        <td>Sent <?php echo timestamp($thread->timestamp); ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>