<div id="right">
  <h1><?php echo $pilot->name;?></h1>
  <span id='fingerprint'>Fingerprint <?php echo $pilot->fingerprint;?></span>

  <ul class="dot-leader">
    <li>
      <span>Credits</span>
      <span><?php echo credits($pilot->credits); ?></span>
    </li>
    <li>
      <span>Legal</span>
      <span><?php echo $pilot->legal; ?></span>
    </li>
    <li>
      <span>Government</span>
      <span><?php echo $pilot->govt->name;?></span>
    </li>
    <li>
      <span>Status</span>
      <span>On <?php echo $pilot->spobname;?></span>
    </li>
  </ul>

  <ul class="options">
    <li><a disabled='true'>Self Destruct</a></li>
    <li><a href='about' class='page'>About</a></li>
  </ul>
</div>