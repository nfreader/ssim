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

function loadPage(page, qs) {
  qs = typeof qs !== 'undefined' ? qs : '';
  $('#game').empty().load("view/" + page + ".php?" + qs);
  console.log("view/" + page + ".php?" + stripHTML(qs));
  $('#spinner').removeClass('fa-spin');
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
  $.ajax({
    type: "GET",
    url: "view/action.php?action="+action,
    success: function(retval) {
      loadView(dest,"msg="+encodeURIComponent(retval)+"&"+query);
    },
    error: function(retval) {
      $('#game').empty().html(retval);
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
  console.log(data);
  if (isJSON(data)) {
    $.each($.parseJSON(data), function(n, m) {
      if (!nativeNotify(m.message)) {
        var color = notifyLevel(m.level);
        var html = "<li class='color " + color + " notify-unread'>" + m.message +"</li>";
        $('.headerbar .msglist').append(html);
        console.log(m.message + ' : ' + m.level);
      }
    });
  } else {
    var html = "<li class='color green notify-unread'>" + data + "</li>";
    $('.headerbar .msglist').append(html);
    console.log(data + ': normal');
  }
}

function addNotification(message, level){
  var color = notifyLevel(level);
  var html = "<li class='color " + color + " notify-unread'>" + message +"</li>";
  $('.headerbar .msglist').append(html);
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

function setContent(selector,html) {
  $(selector).empty().html(html);
}

function loadContent(selector, content) {
  $(selector).empty().load('view/'+content+'.php');
}

function jumpComplete(msg) {
    $('#game').empty().load('view/home.php?msg=' + encodeURIComponent(msg));
    console.log(this.url);
    console.log(msg);
}

$('body').delegate('.headerbar .msglist .color','click', function(){
  $(this).removeClass('notify-unread');
  $(this).addClass('notify-read');
});

function footerInject(content) {
    $('.footerbar .pull-right').html(content);
}

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


$('body').delegate('#singleField', 'keypress', function(event) {
    if (event.which == 13) {
        event.preventDefault();
        var data = $(this).serialize();
        var action = $(this).attr('data-action');
        var dest = 'home';
        $.ajax({
            type: "GET",
            data: data,
            url: "view/action.php?action="+action,
            success: function(retval) {
              loadView(dest,"msg="+encodeURIComponent(retval));
              console.log(data);
            },
            error: function(retval) {
              $('#game').empty().html(retval);
              console.log(retval);
            }
          })
    }
});

$('body').delegate('.commodity.jettison form input','keyup',function(){
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
      if('newmsg' == data.key){
        addNotification(data.value,1);
      }
      console.log(data);
    }
  })
}
setInterval(ping, 10000);

function loadCreateJS() {
     var len = $('script').filter(function() {
         return ($(this).attr('src') == '//code.createjs.com/easeljs-0.8.1.min.js');
     }).length;
     if (len === 0) {
         var script = document.createElement('script');
         script.type = 'text/javascript';
         script.src = '//code.createjs.com/easeljs-0.8.1.min.js';
         document.body.appendChild(script);
     }
 }
