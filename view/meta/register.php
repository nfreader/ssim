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
    <form class="async" action="register" data-dest="home">
      <input name="username" type="text" placeholder="Username" />
      <input name="email" type="email" placeholder="Email Address" />
      <input name="password" type="password" placeholder="Password" />
      <input name="password-again" type="password" placeholder="Password Again" />
      <!-- <p>The following questions are designed to elicit a measureable emotional response. Factors such as pupil dilation, beathing patterns and blood pressure will be used to determine your humanity, or lack thereof. Please remain calm for the duration of this test.</p> -->

      <?php //echo getVKPrompt(); ?>

      <button>Confirm</button>

    </form>
    <p>Any unauthorized access will be viewed as hostile and necessary defensive actions will be taken.</p>
    <p class="ooc">
      I swear I will never, ever sell your information or automatically subscribe you to any future newsletters.
    </p>

    <?php if (SSIM_DEBUG) :?>
      <p class="ooc">This game is still in development mode. Data may be lost, changed or straight up deleted without notice.</p>
    <?php endif;?>
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
