<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Spot That Fire - Whitespace</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery/jquery-3.3.1.js"></script>
<script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?key=AlkQSf2WwyWqmfhNff-HT_ZJ23yrjexI7SzU0pZMoezJdoXRB8gGy4Hh8PLrgijP"></script>
<script type="text/javascript">
var map;
var searchManager;
var mapMode = true;

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

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position) {
      var p = new Microsoft.Maps.Location(position.coords.latitude, position.coords.longitude);
      setP(p);
    });
  } else {
    var p = new Microsoft.Maps.Location(51.50632, -0.12714);
    setP(p);
  }
  function setP(p) {
    var pushpin = new Microsoft.Maps.Pushpin(p, { text: "A", title: "Title", subTitle: "Subtitle" });
    map.entities.push(pushpin);
  
    var infobox = new Microsoft.Maps.Infobox(p, { title: "Infobox",
      description: "London", visible: false });
    infobox.setMap(map);
  
    Microsoft.Maps.Events.addHandler(pushpin, "click", function () {
      infobox.setOptions({ visible: true });
    });
  
    map.setView({
      center: p
    });
  }
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

  <div id="myMap" style="width: 100vw; height: 100vh;"></div>
</div>
<div id="listContainer" style="display: none;">
  <div id="myList" style="width: 100vw; height: 100vh; max-height: 100vh; overflow: scroll;">
    <ul class="list-group">
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
<div id="menuContainer" class="text-center" style="width: 100vw; position: absolute; left: 0; bottom: 10px; z-index: 999;">
  <div class="btn-group btn-group-toggle" data-toggle="buttons">
  <label class="btn btn-secondary active">
    <input type="radio" name="options" id="option1" autocomplete="off" checked>Button
  </label>
  <label class="btn btn-secondary">
    <input type="radio" name="options" id="option2" autocomplete="off">Button
  </label>
  <label class="btn btn-secondary">
    <input type="radio" name="options" id="option3" autocomplete="off">Button
  </label>
  </div>
</div>
</body>
</html>

