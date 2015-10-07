<?php 
  $spobs = new spob();
  $spobs = $spobs->getHomeworlds(); 

  if ($spobs === array()) {
    echo "No homeworlds found!";
    if ($user->isAdmin()) {
      echo "<a href='admin/galaxy' class='load'>Edit Galaxy</a>";
    }
  }
?>
  <div class="form-group">
    <h2 class='form-title'>Request Pilot License</h2>
    <form class="vertical async"
    action="newPilot"
    data-dest="home">
      <input name="firstname" type="text" placeholder="First Name" />
      <input name="lastname" type="text" placeholder="Last Name" />
      <button>Submit Request</button>
    </form>
    <p>By submitting this application you do swear and affirm to adhere to the guidelines set forth by the Interstellar Commerce Treaty (I.C.T.) § 11-38-13 regarding pilot conduct. Failure to follow these guidelines will result in revocation of your license and will be enforced by signatory members of the I. C. T.</p>

    <p class="ooc">
      Your name should be real (not your actual name). Pilots with joke names or names that wouldn't be considered a real name will be changed or deleted
    </p>

