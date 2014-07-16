<?php
  include '../inc/config.php';
?>

<div class="biglogin">
<h1>S.I.M.S. V. <?php echo GAME_VERSION; ?></h1>
  <div class="form-group">
    <h2 class='form-title load'
    page='login'
    content='.form-group'
    dest='.form-group'>Identify</h2>
    <h2 class='form-title'>Create Identity</h2>
    <form class="vertical async-form"
    action="register"
    pass="home"
    fail="loginerror"> 
      <input name="username" type="text" placeholder=">Username" />
      <input name="email" type="email" placeholder=">Email Address" />
      <input name="password" type="password" placeholder=">Password" />
      <input name="password-again" type="password" placeholder=">Password Again" />
      <button>Confirm</button>
    </form>
    <p>Ship Integrated Management System V. <?php echo GAME_VERSION; ?> is © <?php echo $year; ?> by Chekov Armaments LTD. All rights reserved.</p>
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

