<?php
require_once('../../inc/config.php');
$pilot = new pilot(null, false); ?>

<div id="left">
  <ul class="options">
    <li><a class="page" href="home">Back</a></li>
  </ul>
</div>

<div id="center">
  <h1>New Message Beacon</h1>
  <span class="fingerprint">Launching in <?php echo $pilot->systname;?></span>

  <form class="async" action="newBeacon" data-dest="home">
    <label for="content">Message</label>
    <textarea name="content" placeholder="Enter your message"></textarea>
    <button>Launch Beacon</button>
  </form>

</div>

<?php require_once('../rightbar.php');?>
