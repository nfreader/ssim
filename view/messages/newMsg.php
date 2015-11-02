<h1>Draft new message</h1>

<script>
var pilots = <?php echo json_encode($pilot->getPilotList()); ?>;
var html = '<option></option>';
for (var i = pilots.length - 1; i >= 0;  i--) {
  var option = "<option value='"+pilots[i].uid+"'>"+pilots[i].name+"</option>";
  html = html + option;
  console.log(option);
}
$('#pilotSelect').html(html);
</script>

<form class="async" action="sendMsg" data-dest="messages/messages">
<label for="to">Recepient</label>
  <div class="select">
    <select name="to" id="pilotSelect">
    </select>
  </div>
  <br>
  <label for="message">Message</label>
  <textarea name="message" placeholder="Enter your message"></textarea>
  <button>Send</button>
</form>
