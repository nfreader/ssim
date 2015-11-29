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
    <h2 class='form-title'>Identify</h2>
    <h2><a class="page" href='meta/register'>Create Identity</a></h2>
    <form class="async" action="login" data-dest="home">
      <input name="username" type="text" placeholder="Username" />
      <input name="password" type="password" placeholder="Password" />
      <button>Confirm</button>
    </form>
    <p>Any unauthorized access will be viewed as hostile and necessary defensive actions will be taken.</p>
  </div>

</div>
<div class="rightbar empty"></div>
<script>
  loadContent('.footerbar','footer');
</script>
