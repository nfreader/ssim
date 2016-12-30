<?php
include '../../inc/config.php';

?>

<div id="left" class="full">
  <h1>Authenticate with S.I.M.S.</h1>
  <p class="red blink">Chekhov Armaments Internal Security Division Notice:<br>
  Any unauthorized network access will be viewed as hostile and necessary defensive actions will be taken.</p>
  <form class="async" action="login" data-dest="home">
    <input name="username" type="text" placeholder="Username" />
    <input name="password" type="password" placeholder="Password" />
    <button>Confirm</button>
  </form>
  <p class="small">Ship Integrated Management System (S.I.M.S.) network is <i class="fa fa-copyright"></i> <?php echo GAME_YEAR;?> Chekhov Armaments Corporation (C.A.C.). Under terms of the Interstellar Commerce Treaty (I.C.T.) (§II, Article 5, Parts 6-18) and as jointly authorized by the Orion Republic of Federated Systems (O.R.F.S.) and the Carbyne Confederacy of Planets (C.C.P.), C.A.C. Internal Security and Assurance is authorized to issue sanctions against any person or persons determined to be a threat to the S.I.M.S. network.</p>
</div>

<div id="right" class="full">
  <h1>Request new S.I.M.S. access account</h1>
  <p class="red blink">Chekhov Armaments Internal Security Division Notice:<br>
  Any unauthorized network access will be viewed as hostile and necessary defensive actions will be taken.</p>
  <form class="async" action="register" data-dest="home">
    <input name="username" type="text" placeholder="Username" />
    <input name="email" type="email" placeholder="Email Address" />
    <input name="password" type="password" placeholder="Password" />
    <input name="password-again" type="password" placeholder="Password Again" />
    <!-- <p>The following questions are designed to elicit a measureable emotional response. Factors such as pupil dilation, beathing patterns and blood pressure will be used to determine your humanity, or lack thereof. Please remain calm for the duration of this test.</p> -->

    <?php //echo getVKPrompt(); ?>

    <button>Confirm</button>

  </form>

  <p class="small">Ship Integrated Management System (S.I.M.S.) network is <i class="fa fa-copyright"></i> <?php echo GAME_YEAR;?> Chekhov Armaments Corporation (C.A.C.). Under terms of the Interstellar Commerce Treaty (I.C.T.) (§II, Article 5, Parts 6-18) and as jointly authorized by the Orion Republic of Federated Systems (O.R.F.S.) and the Carbyne Confederacy of Planets (C.C.P.), C.A.C. Internal Security and Assurance is authorized to issue sanctions against any person or persons determined to be a threat to the S.I.M.S. network.</p>

  <p class="ooc">
    I swear I will never, ever sell your information or automatically subscribe you to any future newsletters.
  </p>

  <?php if (SSIM_DEBUG) :?>
    <p class="ooc">This game is still in development mode. Data may be lost, changed or straight up deleted without notice.</p>
  <?php endif;?>
</div>
<script>
  loadContent('.footerbar','footer');
</script>
