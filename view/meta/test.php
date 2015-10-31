<?php 

require_once('../../inc/config.php');

?>
  <div id="log"></div>

  <script id="worker1" type="javascript/worker">
    self.addEventListener('message', function(e) {
      var data = e.data;
      switch (data.cmd) {
        case 'start':
          fetch('ping.json')
            .then(
              function(response){
                if (response.status !== 200) {
                  var message = "That didn't work: "+ response.status;
                  self.postMessage('WORKER STARTED: ' + message);
                  return message;
                }
                response.json().then(function(data){
                  var message = data;
                  self.postMessage('WORKER STARTED: ' + message);
                  return message;
                })
              }
            )
            .catch(function(err) {  
              self.postMessage('Fetch Error :-S', err);
            });
          break;
        case 'stop':
          self.postMessage('WORKER STOPPED: ' + data.msg +
                           '. (buttons will no longer work)');
          self.close(); // Terminates the worker.
          break;
        default:
          self.postMessage('Unknown command: ' + data.msg);
      };
    }, false);
  </script>

  <script>
    function log(msg) {
      // Use a fragment: browser will only render/reflow once.
      var fragment = document.createDocumentFragment();
      fragment.appendChild(document.createTextNode(msg));
      fragment.appendChild(document.createElement('br'));

      document.querySelector("#log").appendChild(fragment);
    }

  </script>

  <button onclick="sayHI()">Say HI</button>
  <button onclick="unknownCmd()">Send unknown command</button>
  <button onclick="stop()">Stop worker</button>
  <output id="result"></output>

  <script>
    function sayHI() {
      worker.postMessage({'cmd': 'start', 'msg': 'Hi'});
    }

    function stop() {
      // worker.terminate() from this script would also stop the worker.
      worker.postMessage({'cmd': 'stop', 'msg': 'Bye'});
    }

    function unknownCmd() {
      worker.postMessage({'cmd': 'foobard', 'msg': '???'});
    }

     var blob = new Blob([document.querySelector('#worker1').textContent]);

    var worker = new Worker(window.URL.createObjectURL(blob));

    worker.addEventListener('message', function(e) {
      document.getElementById('result').textContent = e.data;
    }, false);
  </script>