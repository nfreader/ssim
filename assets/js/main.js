WebFontConfig = {
    google: {
        families: ['Share+Tech+Mono::latin']
    },
    timeout: 1000,
    active: function() {
        $('.loading').slideUp(250);
    },
    inactive: function() {
        $('.loading').slideUp(250);
    }
};
(function() {
    var wf = document.createElement('script');
    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
        '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'false';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
})();

function stripHTML(dirtyString) {
    var container = document.createElement('div');
    container.innerHTML = dirtyString;
    return container.textContent || container.innerText;
}

function loadView(view,qs) {
  qs = typeof qs !== 'undefined' ? qs : '';
  $("#game").load("view/"+ view +".php?"+qs, function(R,S,X){
    if (S == "error"){
      $("#game").empty().load("view/error.php?"+qs);
      console.log("Attempted to load: view/"+ view +".php?"+stripHTML(decodeURIComponent(qs)));
      console.log(R);
      console.log(S);
      console.log(X);
    }
  });
  console.log("Loaded view: view/"+ view +".php?"+stripHTML(decodeURIComponent(qs)));
  $('#spinner').removeClass('fa-spin');
}

$('body').delegate('.async','submit',function(){
  event.preventDefault();
  $('#spinner').addClass('fa-spin');
  var action = $(this).attr('action');
  var data = $(this).serialize();
  var dest = $(this).attr('data-dest');
  dest = typeof dest !== 'undefined' ? dest : 'home';
  console.log(action+data+dest);
  $.ajax({
    type: "POST",
    url: "view/action.php?action="+action,
    data: data,
    success: function(retval) {
      loadView(dest,"msg="+encodeURIComponent(retval));
    },
    error: function(retval) {
      $('#game').empty().html(retval);
    }
  });
});

$('body').delegate('.action','click',function(){
  event.preventDefault();
  $('#spinner').addClass('fa-spin');
  var action = $(this).attr('href');
  var dest = $(this).attr('data-dest');
  var query = $(this).attr('data-query');
  query = typeof query !== 'undefined' ? query : '';
  console.log(action+query+' -> '+dest);
  $.ajax({
    type: "GET",
    url: "view/action.php?action="+action,
    success: function(retval) {
      loadView(dest,"msg="+encodeURIComponent(retval)+"&"+query);
    },
    error: function(retval) {
      $('#game').empty().text(retval);
      console.log(retval);
    }
  })
})

$('body').delegate('.page', 'click', function() {
    event.preventDefault();
    $('#spinner').addClass('fa-spin');
    var href = $(this).attr('href');
    var query = $(this).attr('data');
    loadView(href,query);
    console.log('view/' + href + '.php?' + query);
})

function notifyLevel(level){

  switch(level) {

    case 'info':
    case 0:
    return 'navy';

    case 'normal':
    case 1:
    default:
    return 'green';

    case 'warn':
    case 2:
    return 'orange';

    case 'emergency':
    case 3:
    return 'red';
  }

}

function isJSON(json) {
  try {
    JSON.parse(json);
  } catch(e) {
    return false;
  }
  return true;
}

function notify(data) {
  data = decodeURIComponent(data);
  if (isJSON(data)) {
    $.each($.parseJSON(data), function(n, m) {
      if (undefined == m.level) {
        console.log(m);
      } else {
        addNotification(m.message,m.level);
      }
    });
  }
}

function addNotification(message, level){
  var color = notifyLevel(level);
  var html = "<li class='color " + color + " unread'>" + message +"</li>";
  $('#notifications').append(html);
  console.log(message + ' : ' + level);
}

function nativeNotify(message) {
  // Let's check if the browser supports notifications
  if (!("Notification" in window)) {
    return false;
  }

  // Let's check whether notification permissions have already been granted
  else if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    var notification = new Notification(stripHTML(message));
    setTimeout(notification.close.bind(notification), 5000);
    return true;
  }

  // Otherwise, we need to ask the user for permission
  else if (Notification.permission !== 'denied') {
    Notification.requestPermission(function (permission) {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        var notification = new Notification(stripHTML(message));
        return true;
      }
    });
  }

  // Finally, if the user has denied notifications and you
  // want to be respectful there is no need to bother them any more.
}

function jumpComplete(msg) {
    $('#game').empty().load('view/home.php?msg=' + encodeURIComponent(msg));
    console.log(this.url);
    console.log(msg);
}

$('body').delegate('#notifications li','click', function(){
  $(this).removeClass('unread');
  $(this).addClass('read');
});

$('.helpText').click(function() {
    $(this).hide();
});
$('.helpText').hide();

 $('body').delegate('.notice', 'click', function(e) {
    var title = $(this).attr('title');
    var original = $(this).text();
    $('.helpText').show().text(title).css({
        position: "absolute",
        left: e.pageX,
        top: e.pageY
    });
});

$('body').delegate("[disabled='true']", 'click', function() {
    event.preventDefault();
    return false;
})


$('body').delegate('#singleField', 'click', function(event) {
  event.preventDefault();
  if ($(this).data('clicked')) {
    return;
  }
  var tgt = $(this).attr('data');
  $(this).data('tgt',tgt);
  console.log(this);
  var data = stripHTML($(this).text().trim());
  var action = $(this).attr('data-action');
  var html = "<input type='text' name='' value='"+data+"' class='singleInput'>";
  $(this).html(html);
  $(this).data('clicked', true);
  console.log($(this));
});

$('body').delegate('.singleInput','keypress', function(event){
  if (event.keyCode == 13) {
    event.preventDefault();
    var content = $(this).val();
    var tgt = $(this).data('tgt');
    $('#singleField[data="'+tgt+'"]').text(content);
    console.log(content+tgt);
  }
});

$('body').delegate('.commodity.jettison form input','keyup',function(event){
  var value = parseFloat($(this).val());
  var max = parseFloat($(this).attr('max'));
  var supply = parseFloat($(this).attr('data-supply'));
  var btn = $(this).next('button');
  if (value > max) {
    $(btn).prop({
      disabled: true
    }).text('ERROR');
  } else if (value > supply) {
    $(btn).prop({
      disabled: true
    }).text('ERROR');
  } else if (value < 0) {
    $(btn).prop({
      disabled: true
    }).text('Quantum Error');
  } else if (value == 0 || isNaN(value)) {
    $(btn).prop({
      disabled: true
    }).text('Enter amount');
    value = max;
  } else if (value == 1){
    $(btn).prop({
      disabled: false
    }).text('Jettison '+ value +' ton');
  } else {
    $(btn).prop({
      disabled: false
    }).text('Jettison '+ value +' tons');
  }
})

function ping() {
  var data = $.ajax({
    type: "GET",
    url: "view/meta/ping.php",
    dataType: 'json',
    success: function(data) {
      if('newmsg' == data.ping.key){
        addNotification(data.ping.value,1);
      }
      var clientTime = Date.now() / 1000 | 0;
      var diff = clientTime - data.timestamp;
      $('#spinner').attr('title','Latency: '+diff+' secs.');
      if ((diff) <= 2) {
        $('#spinner').removeClass('red');
        $('#spinner').removeClass('orange');
        $('#spinner').addClass('green');
      } else if ((diff >= 3) && (diff <= 4)){
        $('#spinner').removeClass('red');
        $('#spinner').addClass('orange');
        $('#spinner').removeClass('green');
      } else {
        $('#spinner').addClass('red');
        $('#spinner').removeClass('orange');
        $('#spinner').removeClass('green');
      }
    }
  });
}
setInterval(ping, 10000);