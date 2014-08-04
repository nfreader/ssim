<?php
include '../inc/config.php';

?>

<div class="colorbars">
  <h1>INPUT 1</h1>
  <div class="first-row gray"></div>
  <div class="first-row yellow"></div>
  <div class="first-row cyan"></div>
  <div class="first-row green-cb"></div>  
  <div class="first-row magenta"></div>
  <div class="first-row red-bar"></div>
  <div class="first-row blue"></div>

  <div class="second-row blue"></div>
  <div class="second-row black"></div>
  <div class="second-row magenta"></div>
  <div class="second-row black"></div>  
  <div class="second-row cyan"></div>
  <div class="second-row black"></div>
  <div class="second-row white"></div>    

  <div class="third-row white"></div>
  <div class="third-row gray-1"></div>
  <div class="third-row gray-2"></div>
  <div class="third-row gray"></div>  
  <div class="third-row gray-3"></div>
  <div class="third-row gray-4"></div>
  <div class="third-row black"></div>  
</div>

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
    <form class="vertical async-form" action="route.php?action=login" method="POST" page="home">
      <input name="username" type="text" placeholder="Username" />
      <input name="password" type="password" placeholder="Password" />
      <button>Confirm</button>
    </form>
    <p>Any unauthorized access will be viewed as hostile and necessary defensive actions will be taken.</p>
  </div>

</div>
<script>
  loadContent('ping', '.footer', '.footerbar');
</script>
