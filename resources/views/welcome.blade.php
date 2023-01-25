@extends('layouts.header')
<!DOCTYPE html>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
        <form>
          <label>Start Location: <input id="start" type="text" autocomplete="on"></label><br>
          <label>End Location: <input id="end" type="text" autocomplete="on"></label><br>
          <button id="submit">Submit</button>
        </form>
        <div id="map"></div>
        <style>
          #map{
            width: 400px;
            height: 400px; 
          }
        </style>
  <div id="dashboard" style="display: none;">
  <table>
    <tr>
      <th>Name</th>
      <th>Address</th>
    </tr>
    <tbody id="waypoints-table"></tbody>
  </table>
  </div>
  <button id="route-button" style="display: none;">Display Route!</button>
<script>

var waypointsLocation = new Array();

function initMap(){
  var directionsService = new google.maps.DirectionsService();
  var directionsRenderer = new google.maps.DirectionsRenderer();
  var startAutocomplete = new google.maps.places.Autocomplete(document.getElementById("start"));
  var endAutocomplete = new google.maps.places.Autocomplete(document.getElementById("end"));

  var map = new google.maps.Map(document.getElementById("map"), {
    zoom: 7,
    center: {lat: 41.85, lng: -87.65}
  });
  directionsRenderer.setMap(map);
  document.getElementById('map').style.display = 'none';
  document.getElementById("submit").addEventListener("click", function(event){
    event.preventDefault();

    var start = document.getElementById("start").value;
    var end = document.getElementById("end").value;

    directionsService.route({
      origin: start,
      destination: end,
      travelMode: "DRIVING"
    }, function(response, status) {
      if (status === "OK") {
    var service = new google.maps.places.PlacesService(map);
    var path = response.routes[0].overview_path;
    var promises = [];
    var step = path.length / 10;
    for (let i = 0; i < path.length; i += step) {
      promises.push(nearbySearch(service, path[Math.round(i)]));
    }
    Promise.all(promises)
        .then(function(results) {
            waypoints = [].concat(...results);
            var uniqueWaypoints = filterUniqueWaypoints(waypoints);
            displayDashboard(uniqueWaypoints);
            document.getElementById("route-button").addEventListener("click", function(){
              document.getElementById('dashboard').style.display = 'none';
              document.getElementById('route-button').style.display = 'none';
              document.getElementById('map').style.display = 'block';
              directionsService.route({
              origin: start,
              destination: end,
              waypoints: Array.from(waypointsLocation),
              optimizeWaypoints: true,
              travelMode: "DRIVING"
            }, function(response, status) {
              if (status === "OK") {
                directionsRenderer.setDirections(response);
              }
            });
            })
        });
      };
    });
  });
};


function nearbySearch(service, location) {
  return new Promise(function(resolve, reject) {
    setTimeout(function() {
      service.nearbySearch({
      location: location,
      radius: 5000,
      type: ["museum"]
  }, function(results, status) {
      if (status === "OK") {
          resolve(results);
      } else {
          reject(status);
      }
  });
    }, 1000);
  });
}

function displayDashboard(waypoints) {
  document.getElementById('dashboard').style.display = 'block';
  document.getElementById('route-button').style.display = 'block';
  var visitedWaypoints = [];
  var table = document.getElementById("waypoints-table");
  waypoints.forEach(function(waypoint) {
    var row = table.insertRow();
    var nameCell = row.insertCell(0);
    var addressCell = row.insertCell(1);
    var visitCell = row.insertCell(2);

    nameCell.innerHTML = waypoint.name;
    addressCell.innerHTML = waypoint.vicinity;

    var visitButton = document.createElement("button");
    var routeButton = document.getElementById("route-button")
    visitButton.innerHTML = "Visit";
    routeButton.innerHTML = "Create Route"
    visitButton.onclick = function() {
      if(visitedWaypoints.length < 7){
      visitedWaypoints.push(waypoint);
      row.remove();
      }else if(visitedWaypoints.length == 7){
        visitedWaypoints.push(waypoint);
        row.remove();
        document.getElementById("dashboard").style.display = "none";
      };
      waypointsLocation.push({location: waypoint.geometry.location});
    }
    visitCell.appendChild(visitButton);
  });
}

function filterUniqueWaypoints(waypoints) {
  var uniqueWaypoints = [];
  var uniqueIds = new Set();
  waypoints.forEach(function(waypoint) {
    if (!uniqueIds.has(waypoint.place_id)) {
      if(!waypoint.business_status.includes('CLOSED')){
        uniqueIds.add(waypoint.place_id);
        uniqueWaypoints.push(waypoint);
      };
    }
  });
  return uniqueWaypoints;
}

</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=initMap"
async defer>
</script>
        </div>
    </div>
</div>
</body>
</html>