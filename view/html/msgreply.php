<?php
echo "<form action='view/action.php?action=sendMsg&to=$to'
class='form async-form msg-reply' page='messages'>";
echo "<h3>Reply to $name</h3>";
echo "<textarea name='message' placeholder='Enter your message'></textarea>";
echo "<button>Send</button>";
echo "</form>";
