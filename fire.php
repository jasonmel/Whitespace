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
var id = getCookie("id");
var map;
var searchManager;
var mapMode = true;
var centerPushpin;
var pushpins = [];
var infoboxes = [];

function getStatusEmoji(status) {
  var emoji = "&#x1F525;"; // Fire - https://emojipedia.org/fire/
  if (status == 1) {
    emoji = "&#x1F692;"; // Fire Engine - https://emojipedia.org/fire-engine/
  } else if (status == 2) {
    emoji = "&#x1F4A7;"; // Droplet - https://emojipedia.org/droplet/
  }

  return emoji;
}

function getStatusTag(status) {
  var tag = '<span class="status onfire">On Fire</span>';
  if (status == 1) {
    tag = '<span class="status extinguishing">Extinguishing</span>';
  } else if (status == 2) {
    tag = '<span class="status extinguished">Extinguished</span>';
  }

  return tag;
}

function getFeedback(fire, id) {
  var likedClass = (fire.liked) ? ' class="liked"' : '';
  var dislikedClass = (fire.disliked) ? ' class="disliked"' : '';
  var feedback = '<span' + likedClass + '>&#x1F44D; ' + fire.like + '</span> &nbsp; <span' + dislikedClass + '>&#x1F44E; ' + fire.dislike + '</span>';

  return feedback;
}

function getName(fire) {
  var name = '<span class="name">' + fire.reporter + '</span>';

  return name;
}

function getTime(fire) {
  var timeStr = fire.time.date;
  var time = '<time class="time">' + timeStr.substring(0, timeStr.lastIndexOf(":")) + '</time>';

  return time;
}

function initFires() {
  pushpins = [];
  infoboxes = [];

  // get all fires
  map.entities.clear();
  $("#myListUl").empty();
  $.getJSON( "api/getAllFires.php", function( data ) {
    for (var i = 0; i < data.length; i++) {
      var fire = data[i];
      addFireOnMap(fire, i);
      addFireOnList(fire, i);
    }
  });
  function addFireOnMap(fire, i) {
    var statusEmoji = getStatusEmoji(fire.status);
    var statusTag = getStatusTag(fire.status);
    var feedback = getFeedback(fire, i);
    var name = getName(fire);
    var time = getTime(fire);

    var p = new Microsoft.Maps.Location(fire.lat, fire.lon);
    var pushpin = new Microsoft.Maps.Pushpin(p, { icon: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"><text x="0" y="28" style="font-size: 30px">' + statusEmoji + '</text></svg>', title: fire.title, subTitle: null });
    map.entities.push(pushpin);
    pushpins.push(pushpin);
  
    var infobox = new Microsoft.Maps.Infobox(p, { title: fire.title,
      description: '<div clsas="header">' + statusTag + '</div><hr><div class="body">' + fire.description + '</div><hr><div class="footer"><div class="left">' + feedback + '</div><div class="right">' + name + '<br>' + time + '</div></div>',
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
    });
  }
  function addFireOnList(fire, i) {
    var statusEmoji = getStatusEmoji(fire.status);
    var statusTag = getStatusTag(fire.status);
    var feedback = getFeedback(fire, i);
    var name = getName(fire);
    var time = getTime(fire);

    var li = $('<a href="#" class="list-group-item list-group-item-action"></a>');
    li.append($('<div class="header">' + statusEmoji + ' ' + fire.title + '</div><div class="body" style="padding: 5px 0 12px 0;">' + fire.description + '</div><div class="footer"><div class="left">' + feedback + '</div><div class="right">' + name + ' · ' + time + '</div></div>'));
    li.click(function(e) {
      $("#option1").click();
      Microsoft.Maps.Events.invoke(pushpins[i], 'click', { target: pushpins[i] });
    });
    $("#myListUl").append(li);
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
    $.post( "api/report.php", {
      reporter: id,
      lat: mapCenter.latitude,
      lon: mapCenter.longitude,
      title: $("#title").val(),
      description: $("#description").val(),
    }).done(function( data ) {
      // TODO: Thank you...
      $("#reportModal").hide();
      initFires();
    });
  });

  $("#idSubmitButton").click(function(e) {
    $.get( "setcookie.php?id=" + $("#id").val(), function( data ) {
      id = $("#id").val();
      $("#idModal").hide();
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

    $("#reportModal").show();
    $("#idModal").hide();
  });

  $("#option3").click(function(e) {
    if (!mapMode) {
      $("#option1").click();
    }

    $("#reportModal").hide();
    $("#idModal").show();
  });

  $("#reportCloseButton").click(function(e) {
    $("#reportModal").hide();
  });

  $("#idCloseButton").click(function(e) {
    $("#idModal").hide();
  });

  if (id == null) {
    $("#option3").click();
  } else {
    $("#id").val(id);
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
              <input type="text" class="form-control" id="title" name="title" maxlength="30" placeholder="Fire Title...">
              <small id="titleHelp" class="form-text text-muted">No more than 30 characters...</small>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3" placeholder="Fire Description...."></textarea>
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
              <label for="id">My ID</label>
              <input type="text" class="form-control" id="id" name="id" maxlength="20" placeholder="My ID...">
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

