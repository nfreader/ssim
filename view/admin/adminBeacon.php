<?php require_once('adminHeader.php');
$syst = new syst();
?>
<div id="center">
<h1>New Admin Beacon</h1>

<script>
var systems = <?php echo json_encode($syst->listSysts()); ?>;
var html = '<option></option>';
for (var i = systems.length - 1; i >= 0;  i--) {
  var option = "<option value='"+systems[i].id+"'>"+systems[i].name+"</option>";
  html = html + option;
}
$('#systemSelect').html(html);
</script>

<form class="async" action="newAdminBeacon" data-dest="admin/adminBeacon">
<label for="syst">System</label>
  <div class="select">
    <select name="syst" id="systemSelect">
    </select>
  </div>
  <br>
  <label for="content">Message</label>
  <textarea name="content" placeholder="Enter your message"></textarea>
  <button>Launch Beacon</button>
</form>
</div>
