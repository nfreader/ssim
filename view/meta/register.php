<?php
include '../../inc/config.php';

?>

<?php include '../etc/colorbars.php'; ?>
<div class="leftbar empty"></div>
<div class="center login">
<h1>
<?php include '../etc/ca-logo.php';?>
S.I.M.S. V. <?php echo GAME_VERSION;?>
<span class="green pull-right">ONLINE</span>
</h1>
  <div class="form-group">
    <h2><a class='load' href='meta/login'>Identify</a></h2>
    <h2 class='form-title'>Create Identity</h2>
    <form class="vertical async-form"
    action="route.php?action=register" page="login" method="POST ">
      <input name="username" type="text" placeholder="Username" />
      <input name="email" type="email" placeholder="Email Address" />
      <input name="password" type="password" placeholder="Password" />
      <input name="password-again" type="password" placeholder="Password Again" />
      <p>The following question is designed to elicit a measureable emotional response. Factors such as pupil dilation, beathing patterns and blood pressure will be used to determine your humanity, or lack thereof. Please remain calm for the duration of this test.</p>

      <?php //echo getVKPrompt(); ?>

      <button>Confirm</button>

    </form>
    <p>Any unauthorized access will be viewed as hostile and necessary defensive actions will be taken.</p>
  </div>

  <!-- <div class="form-group">
    <h2 class='form-title'>Establish Credentials</h2>
    <form class="vertical" action="" method="POST">
      <input name="username" type="text" placeholder="Username" />
      <input name="email" type="email" placeholder="Email Address" />
      <input name="password" type="password" placeholder="Password" />
      <input name="password-again" type="password" placeholder="Password Verification" />
    </form>
  </div> -->
</div>
<div class="rightbar empty"></div>
<!-- <div class="bigright">

<p>Welcome to the Ship Integrated Management System (SIMS). After confirming your identity, you will be granted remote access to your ship systems.</p>


</div> -->
