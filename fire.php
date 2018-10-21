<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Spot That Fire - Whitespace</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
<style>
.header {
  font-weight: bold;
}
.body {
}
.body .box {
  max-width: 90vw;
  max-height: 20vh;
  overflow: scroll;
}
.footer {
  clear: both;
  font-size: 80%;
  color: gray;
}
.footer .left {
  float: left;
}
.footer .right {
  float: right;
  text-align: right;
}
.footer .name {
  font-weight: bold;
}
.footer .time {
  font-style: italic;
}
.status {
  font-weight: normal;
  font-size: 80%;
  padding: 3px 6px;
  border-radius: 3px;
  color: white;
}
.status.onfire {
  background: IndianRed;
}
.status.extinguishing {
  background: Coral;
}
.status.extinguished {
  background: SeaGreen;
}
.status.fake {
  background: Gray;
}
.opinion {
  cursor: pointer;
}
.liked {
  font-weight: bold;
  padding: 3px 6px;
  border-radius: 3px;
  color: white;
  background: MediumSeaGreen;
}
.disliked {
  font-weight: bold;
  padding: 3px 6px;
  border-radius: 3px;
  color: white;
  background: LightCoral;
}
</style>
<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery/jquery-3.3.1.js"></script>
<!--
<script src="https://cdnjs.cloudflare.com/ajax/abs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
-->
<script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?key=AlkQSf2WwyWqmfhNff-HT_ZJ23yrjexI7SzU0pZMoezJdoXRB8gGy4Hh8PLrgijP"></script>
<script type="text/javascript">
function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return null
}
var uid = getCookie("uid");
var map;
var searchManager;
var mapMode = true;
var centerPushpin;
var fires = [];
var pushpins = [];
var infoboxes = [];
var cards = [];

function getStatusEmoji(fire) {
  var emoji = "&#x1F525;"; // Fire - https://emojipedia.org/fire/
  if (fire.status == 1) {
    emoji = "&#x1F692;"; // Fire Engine - https://emojipedia.org/fire-engine/
  } else if (fire.status == 2) {
    emoji = "&#x1F4A7;"; // Droplet - https://emojipedia.org/droplet/
  } else if (fire.status == 3) {
    emoji = "&#x1F4A9;"; // Pile of Poo - https://emojipedia.org/pile-of-poo/
  }

  return emoji;
}

function updateStatus(eid, value) {
  $.post( "api/solve_event.php", {
    eid: eid,
    uname: uid,
    status: value,
  }).done(function( data ) {
    console.log(data);
  });
}

function getStatusTag(fire) {
  var tag = '<span class="status onfire">On Fire</span>';
  var option0 = '<option value="0">On Fire</option>';
  var option1 = '<option value="1">Extinguishing</option>';
  var option2 = '<option value="2">Extinghished</option>';
  var option3 = '<option value="3">Fake</option>';
  if (fire.status == 1) {
    tag = '<span class="status extinguishing">Extinguishing</span>';
    option1 = '<option value="1" selected>Extinguishing</option>';
  } else if (fire.status == 2) {
    tag = '<span class="status extinguished">Extinguished</span>';
    option2 = '<option value="2" selected>Extinghished</option>';
  } else if (fire.status == 3) {
    tag = '<span class="status fake">Fake</span>';
    option3 = '<option value="3" selected>Fake</option>';
  }

  if (uid == "admin") {
    var startTag = '<select onchange="updateStatus(' + fire.id + ', ' + 'this.value);">';
    var endTag = '</select>';
    tag = startTag + option0 + option1 + option2 + option3 + endTag;
  }

  return tag;
}

function updateOpinion(i, opinion) {
  var fire = fires[i];
  if (opinion == 1) {
    $.post( "api/update_opinion.php", {
      eid: fire.id,
      uname: uid,
      opinion: 1,
    }).done(function( data ) {
      console.log(data);
      if (fire.agree && !fire.disagree) {
        fire.agree = false;
	fire.agreesum -= 1;
      } else if (!fire.agree && fire.disagree) {
        fire.agree = true;
	fire.agreesum += 1;
        fire.disagree = false;
	fire.disagreesum -= 1;
      } else if (!fire.agree && !fire.disagree) {
        fire.agree = true;
	fire.agreesum += 1;
      }

      updateOpinionElements(fire, i);
    });
  } else if (opinion == -1) {
    $.post( "api/update_opinion.php", {
      eid: fire.id,
      uname: uid,
      opinion: -1,
    }).done(function( data ) {
      console.log(data);
      if (fire.agree && !fire.disagree) {
        fire.agree = false;
	fire.agreesum -= 1;
        fire.disagree = true;
	fire.disagreesum += 1;
      } else if (!fire.agree && fire.disagree) {
        fire.disagree = false;
	fire.disagreesum -= 1;
      } else if (!fire.agree && !fire.disagree) {
        fire.disagree = true;
	fire.disagreesum += 1;
      }

      updateOpinionElements(fire, i);
    });
  }
}

function getFeedback(fire, i, mode) {
  var likeId = 'like_' + mode + '_' + i;
  var dislikeId = 'dislike_' + mode + '_' + i;
  var likedClass = (fire.agree) ? ' class="liked"' : '';
  var dislikedClass = (fire.disagree) ? ' class="disliked"' : '';
  var feedback = '<span id="' + likeId + '"' + likedClass + '>&#x1F44D; ' + fire.agreesum + '</span> &nbsp; <span id="' + dislikeId + '"' + dislikedClass + '>&#x1F44E; ' + fire.disagreesum + '</span>';
  if (mode == "map") {
    feedback = '<span id="' + likeId + '"' + likedClass + ' onclick="updateOpinion(' + i + ', 1); return false;">&#x1F44D; ' + fire.agreesum + '</span> &nbsp; <span id="' + dislikeId + '"' + dislikedClass + ' onclick="updateOpinion(' + i + ', -1); return false;">&#x1F44E; ' + fire.disagreesum + '</span>';
  }

  return feedback;
}

function addFeedbackEventHandler(fire, i, mode) {
  var likeId = 'like_' + mode + '_' + i;
  var dislikeId = 'dislike_' + mode + '_' + i;

  $("#" + likeId).click(function(e) {
    updateOpinion(i, 1);

    e.stopPropagation();
    return false;
  });
  $("#" + dislikeId).click(function(e) {
    updateOpinion(i, -1);

    e.stopPropagation();
    return false;
  });
}

function updateOpinionElements(fire, i) {
  var likeMapId = 'like_map_' + i;
  var dislikeMapId = 'dislike_map_' + i;
  var likeListId = 'like_list_' + i;
  var dislikeListId = 'dislike_list_' + i;
  var likeValue = '&#x1F44D; ' + fire.agreesum;
  var dislikeValue = '&#x1F44E; ' + fire.disagreesum;

  if (fire.agree) {
    $("#" + likeMapId).addClass("liked");
    $("#" + likeListId).addClass("liked");
  } else {
    $("#" + likeMapId).removeClass("liked");
    $("#" + likeListId).removeClass("liked");
  }
  $("#" + likeMapId).empty();
  $("#" + likeListId).empty();
  $("#" + likeMapId).html(likeValue);
  $("#" + likeListId).html(likeValue);

  if (fire.disagree) {
    $("#" + dislikeMapId).addClass("disliked");
    $("#" + dislikeListId).addClass("disliked");
  } else {
    $("#" + dislikeMapId).removeClass("disliked");
    $("#" + dislikeListId).removeClass("disliked");
  }
  $("#" + dislikeMapId).empty();
  $("#" + dislikeListId).empty();
  $("#" + dislikeMapId).html(dislikeValue);
  $("#" + dislikeListId).html(dislikeValue);
}

function getName(fire) {
  var name = '<span class="name">' + fire.reporter + '</span>';

  return name;
}

function getTime(fire) {
  var timeStr = fire.date;
  var time = '<time class="time">' + timeStr.substring(0, timeStr.lastIndexOf(":")) + '</time>';

  return time;
}

function viewComments(eid) {
  $.getJSON( "api/query_comment.php?eid=" + eid, function( data ) {
    console.log(data);
    $("#commentEid").val(eid);
    $("#commentUl").empty();
    for (var i = 0; i < data.length; i++) {
      comment = data[i];

      var li = $('<li class="list-group-item"></li>');
      li.append($('<div class="body" style="padding: 0;">' + comment.comment + '</div><div class="footer"><div class="right"><span class="name">' + comment.name + '</span> · ' + getTime(comment) + '</div></div>'));
      $("#commentUl").append(li);
    }
    $("#commentInput").val("");
    $("#commentModal").show();
  });
}

function initFires() {
  pushpins = [];
  infoboxes = [];
  cards = [];

  // get all fires
  map.entities.clear();
  $("#myListUl").empty();
  $.getJSON( "api/query_event.php?uname=" + uid, function( data ) {
    console.log(data);
    fires = data;
    for (var i = 0; i < data.length; i++) {
      var fire = data[i];
      addFireOnMap(fire, i);
      addFireOnList(fire, i);
    }
  });
  function addFireOnMap(fire, i) {
    var statusEmoji = getStatusEmoji(fire);
    var statusTag = getStatusTag(fire);
    var feedback = getFeedback(fire, i, "map");
    var name = getName(fire);
    var time = getTime(fire);
    var iconSize = 32;
    if (fire.agreesum - fire.disagreesum > 10) {
      iconSize = 64;
    } else if (fire.agreesum - fire.disagreesum > 5) {
      iconSize = 48;
    }

    var p = new Microsoft.Maps.Location(fire.lat, fire.lon);
    var pushpin = new Microsoft.Maps.Pushpin(p, { icon: '<svg xmlns="http://www.w3.org/2000/svg" width="' + iconSize + '" height="' + iconSize + '"><text x="0" y="' + (iconSize - 4) + '" style="font-size: ' + (iconSize - 2) + 'px">' + statusEmoji + '</text></svg>', title: fire.title, subTitle: null });
    map.entities.push(pushpin);
    pushpins.push(pushpin);
  
    var infobox = new Microsoft.Maps.Infobox(p, { title: fire.title,
      description: '<div clsas="header">' + statusTag + '</div><hr><div class="body"><div class="box">' + fire.description + '</div><br><a href="#" style="font-size: 80%;" onclick="viewComments(' + fire.id + '); return false;">View comments</a></div><hr><div class="footer"><div class="left opinion">' + feedback + '</div><div class="right">' + name + '<br>' + time + '</div></div>',
      maxWidth: 600,
      maxHeight: 900,
      visible: false });
    infobox.setMap(map);
    infoboxes.push(infobox);
  
    Microsoft.Maps.Events.addHandler(pushpin, "click", function () {
      for (var j = 0; j < infoboxes.length; j++) {
        infoboxes[j].setOptions({ visible: false });
      }
      infobox.setOptions({ visible: true });

      map.setView({
        center: pushpin.getLocation()
      });
    });

    addFeedbackEventHandler(fire, i, "map");
  }
  function addFireOnList(fire, i) {
    var statusEmoji = getStatusEmoji(fire);
    var statusTag = getStatusTag(fire);
    var feedback = getFeedback(fire, i, "list");
    var name = getName(fire);
    var time = getTime(fire);

    var li = $('<a href="#" class="list-group-item list-group-item-action"></a>');
    li.append($('<div class="header">' + statusEmoji + ' ' + fire.title + '</div><div class="body" style="padding: 5px 0 12px 0;">' + fire.description + '</div><div class="footer"><div class="left opinion">' + feedback + '</div><div class="right">' + name + ' · ' + time + '</div></div>'));
    li.click(function(e) {
      $("#option1").click();
      Microsoft.Maps.Events.invoke(pushpins[i], 'click', { target: pushpins[i] });
    });
    $("#myListUl").append(li);
    cards.push(li);

    addFeedbackEventHandler(fire, i, "list");
  }
}

function loadMapScenario() {
  map = new Microsoft.Maps.Map(document.getElementById("myMap"), {});
  Microsoft.Maps.loadModule("Microsoft.Maps.Search", function () {
    searchManager = new Microsoft.Maps.Search.SearchManager(map);
  });

  Microsoft.Maps.loadModule("Microsoft.Maps.AutoSuggest", {
    callback: onLoad,
    errorCallback: onError
  });
  function onLoad() {
    var options = { maxResults: 5 };
    var manager = new Microsoft.Maps.AutosuggestManager(options);
    manager.attachAutosuggest("#searchInput", "#searchContainer", selectedSuggestion);
  }
  function onError(message) {
  }
  function selectedSuggestion(suggestionResult) {
  }

  // init fires
  initFires();

  // get user location and center map on that
  var p = new Microsoft.Maps.Location(25.026249, 121.527511);
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position) {
      p = new Microsoft.Maps.Location(position.coords.latitude, position.coords.longitude);
    });
  }
  map.setView({
    center: p
  });

  // put center pushpin
  Microsoft.Maps.Events.addHandler(map, 'viewchangeend', function (e) {
    map.entities.remove(centerPushpin);
    centerPushpin = new Microsoft.Maps.Pushpin(map.getCenter(), { icon: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"><text x="0" y="28" style="font-size: 30px">&#8853;</text></svg>',
      anchor: new Microsoft.Maps.Point(16, 16),
      title: "Report This Point",
      subTitle: null });
    map.entities.push(centerPushpin);

    Microsoft.Maps.Events.addHandler(centerPushpin, "click", function () {
      $("#option2").click();
    });
  });
}

$(document).ready(function() {
  $("#searchButton").click(function(e) {
    var requestOptions = {
      bounds: map.getBounds(),
      where: $("#searchInput").val(),
      callback: function (answer, userData) {
        map.setView({ bounds: answer.results[0].bestView });
        map.entities.push(new Microsoft.Maps.Pushpin(answer.results[0].location));
      }
    };
    searchManager.geocode(requestOptions);
  });

  $("#reportSubmitButton").click(function(e) {
    var mapCenter = map.getCenter();
    $.post( "api/create_event.php", {
      uname: uid,
      lat: mapCenter.latitude,
      lon: mapCenter.longitude,
      title: $("#title").val(),
      description: $("#description").val(),
    }).done(function( data ) {
      console.log(data);
      // TODO: Thank you...
      $("#reportModal").hide();
      initFires();
    });
  });

  $("#idSubmitButton").click(function(e) {
    $.get( "setcookie.php?uid=" + $("#uid").val(), function( data ) {
      console.log(data);
      uid = $("#uid").val();
      $("#idModal").hide();
    });
  });

  $("#commentSubmitButton").click(function(e) {
    $.post( "api/push_comment.php", {
      eid: $("#commentEid").val(),
      uname: uid,
      comment: $("#commentInput").val(),
    }).done(function( data ) {
      console.log(data);
      var li = $('<li class="list-group-item"></li>');
      li.append($('<div class="body" style="padding: 0;">' + $("#commentInput").val() + '</div><div class="footer"><div class="right"><span class="name">' + uid + '</span> · <span class="time">Now</span></div></div>'));
      $("#commentUl").append(li);
    });
  });

  $("#option1").click(function(e) {
    if (mapMode) {
      $("#listContainer").show();
      $("#mapContainer").hide();
    } else {
      $("#mapContainer").show();
      $("#listContainer").hide();
    }
    mapMode = !mapMode;
  });

  $("#option2").click(function(e) {
    if (!mapMode) {
      $("#option1").click();
    }

    $("#title").val("");
    $("#description").val("");
    $("#reportModal").show();
    $("#idModal").hide();
    $("#commentModal").hide();
  });

  $("#option3").click(function(e) {
    if (!mapMode) {
      $("#option1").click();
    }

    $("#reportModal").hide();
    $("#idModal").show();
    $("#commentModal").hide();
  });

  $("#reportCloseButton").click(function(e) {
    $("#reportModal").hide();
  });

  $("#idCloseButton").click(function(e) {
    $("#idModal").hide();
  });

  $("#commentCloseButton").click(function(e) {
    $("#commentModal").hide();
  });

  if (uid == null) {
    $("#option3").click();
  } else {
    $("#uid").val(uid);
  }
});
</script>
</head>
<body onload="loadMapScenario()" style="margin: 0">
<div id="mapContainer">
  <div id="searchContainer" style="position: absolute; width: calc(100% - 140px); left: 10px; top: 10px; z-index: 999">
    <div class="input-group">
      <input id="searchInput" type="text" class="form-control" placeholder="Search the Map">
      <div id="searchButton" class="input-group-append" style="cursor: pointer;">
        <span class="input-group-text">&#x1F50D;</span>
      </div>
    </div>
  </div>

  <div id="reportModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New Fire Event</h5>
          <button id="reportCloseButton" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label for="title">Title</label>
              <input type="text" class="form-control" id="title" name="title" maxlength="30" placeholder="Fire Title..." required>
              <small id="titleHelp" class="form-text text-muted">No more than 30 characters...</small>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3" placeholder="Fire Description...." required></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button id="reportSubmitButton" type="button" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <div id="idModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Setup My ID</h5>
          <!--
          <button id="idCloseButton" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          -->
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label for="uid">My ID</label>
              <input type="text" class="form-control" id="uid" name="uid" maxlength="20" placeholder="My ID..." required>
              <small id="idHelp" class="form-text text-muted">No more than 20 characters...</small>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button id="idSubmitButton" type="button" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <div id="commentModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Comments</h5>
          <button id="commentCloseButton" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
	  <div class="body">
            <div class="box">
              <ul id="commentUl" class="list-group">
                <li class="list-group-item">Cras justo odio</li>
                <li class="list-group-item">Dapibus ac facilisis in</li>
                <li class="list-group-item">Morbi leo risus</li>
                <li class="list-group-item">Porta ac consectetur ac</li>
                <li class="list-group-item">Vestibulum at eros</li>
                <li class="list-group-item">Cras justo odio</li>
                <li class="list-group-item">Dapibus ac facilisis in</li>
                <li class="list-group-item">Morbi leo risus</li>
                <li class="list-group-item">Porta ac consectetur ac</li>
                <li class="list-group-item">Vestibulum at eros</li>
              </ul>
	    </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="input-group">
            <input id="commentEid" type="hidden" class="form-control" value="0">
            <input id="commentInput" type="text" class="form-control" placeholder="Comment...">
            <div id="commentSubmitButton" class="input-group-append" style="cursor: pointer;">
              <span class="input-group-text">Post</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!--<div style="position: absolute; left: 0; right: 0; margin: auto; z-index: 999;">&#x1F525;</div>-->

  <div id="myMap" style="width: 100vw; height: 100vh;"></div>
</div>
<div id="listContainer" style="display: none;">
  <div id="myList" style="width: 100vw; height: 100vh; max-height: 100vh; overflow: scroll;">
    <div id="myListUl" class="list-group">
      <a href="#" class="list-group-item list-group-item-action">Cras justo odio</a>
      <a href="#" class="list-group-item list-group-item-action">Dapibus ac facilisis in</a>
      <a href="#" class="list-group-item list-group-item-action">Morbi leo risus</a>
      <a href="#" class="list-group-item list-group-item-action">Porta ac consectetur ac</a>
      <a href="#" class="list-group-item list-group-item-action">Vestibulum at eros</a>
    </div>
  </div>
</div>
<div id="menuContainer" class="text-center" style="width: 100vw; position: absolute; left: 0; bottom: 10px; z-index: 999;">
  <div class="btn-group btn-group-toggle" data-toggle="buttons">
  <label class="btn btn-secondary active">
    <input type="radio" name="options" id="option1" autocomplete="off">Map/List
  </label>
  <label class="btn btn-secondary">
    <input type="radio" name="options" id="option2" autocomplete="off">Report
  </label>
  <label class="btn btn-secondary">
    <input type="radio" name="options" id="option3" autocomplete="off">My ID
  </label>
  </div>
</div>
</body>
</html>

