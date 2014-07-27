<?php
include '../inc/config.php';
if (isset($_GET['msg'])) {
	echo '<div class="dialog error">
  <h1>ALERT</h1>
  <p>'.urldecode($_GET['msg']).'</p>
</div>
';
}
?>
<div class="biglogin">
<h1>
<?php include 'ca-logo.php';?>
S.I.M.S. V. <?php echo GAME_VERSION;?>
<span class="green pull-right">ONLINE</span>
</h1>
  <div class="form-group">
    <h2 class='form-title'>Identify</h2>
    <h2 class='load form-title'
    page='register'
    content='.form-group'
    dest='.form-group'>Create Identity</h2>
    <form class="vertical login-form" action="index.php?action=login" method="POST">
      <input name="username" type="text" placeholder="Username" />
      <input name="password" type="password" placeholder="Password" />
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

<!-- <div class="bigright">

<p>Welcome to the Ship Integrated Management System (SIMS). After confirming your identity, you will be granted remote access to your ship systems.</p>


</div> -->

