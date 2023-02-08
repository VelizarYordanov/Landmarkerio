@extends('layouts.header')
<!DOCTYPE html>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
        <form>
          <input id="start" type="text" autocomplete="on" placeholder="Start location"><br>
          <input id="end" type="text" autocomplete="on"><br>
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

var directionsService;
var directionsRenderer;
var waypointsLocation = new Array();

function initMap(){
  directionsService = new google.maps.DirectionsService();
  directionsRenderer = new google.maps.DirectionsRenderer();
  var startAutocomplete = new google.maps.places.Autocomplete(document.getElementById("start"));
  var endAutocomplete = new google.maps.places.Autocomplete(document.getElementById("end"));

  var map = new google.maps.Map(document.getElementById("map"), {
    zoom: 6,
    center: {lat: 42.725, lng: 25.483}
  });
  directionsRenderer.setMap(map);

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
    var timeout;
    for (let i = 0, timeout = 0; i < path.length; i += step, timeout++) {
      promises.push(nearbySearch(service, path[Math.floor(i)], timeout * 200));
    }
    Promise.all(promises)
        .then(function(results) { 
            waypoints = [].concat(...results.filter(function(result){
              return result.length > 0;
            }));
            var uniqueWaypoints = filterUniqueWaypoints(waypoints);
            Dashboard(uniqueWaypoints);
            document.getElementById("route-button").addEventListener("click", function(){
              document.getElementById('dashboard').style.display = 'none';
              document.getElementById('route-button').style.display = 'none';
              updateRoute(start, end, waypointsLocation);
            })
        });
      };
    });
  });
};

function updateRoute(start, end, waypointsLocation){
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
}


function nearbySearch(service, location, timeout) {
  return new Promise(function(resolve, reject) {
   setTimeout(function() {
      service.nearbySearch({
      location: location,
      radius: 5000,
      type: ["museum"]
  }, function(results, status) {
      if (status === "OK") {
          resolve(results);
      }else if(status === "ZERO_RESULTS"){
        resolve([]);
      }
  });
    }, timeout);
  });
}

function getUserId(callback){
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '/get-user-id', true);
  xhr.onreadystatechange = function(){
    if(xhr.readyState === XMLHttpRequest.DONE && xhr.status == 200){
      var userID = JSON.parse(xhr.responseText).id;
      callback(userID);
    }
  }
  xhr.send();
}

function Dashboard(waypoints) {
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

    var favouriteButton = document.createElement("button");
    var visitButton = document.createElement("button");
    var routeButton = document.getElementById("route-button");
    favouriteButton.innerHTML = "Add to favourites";
    visitButton.innerHTML = "Visit";
    routeButton.innerHTML = "Create Route";

    var start = document.getElementById("start").value;
    var end = document.getElementById("end").value;

    visitButton.onclick = function() {
      if (visitedWaypoints.length < 7) {
        visitedWaypoints.push(waypoint);
        row.remove();
        waypointsLocation.push({location: waypoint.geometry.location});
        updateRoute(start, end, waypointsLocation);
      } else if (visitedWaypoints.length === 7) {
        visitedWaypoints.push(waypoint);
        row.remove();
        document.getElementById("dashboard").style.display = "none";
        waypointsLocation.push({location: waypoint.geometry.location});
        updateRoute(start, end, waypointsLocation);
      }

    }

    favouriteButton.onclick = function() {
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      getUserId(userID => {
        fetch('/favourite-places', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({
            user_id: userID,
            place_id: waypoint.place_id
          })
        })
        .then(response => {
          if (response.ok) {
            favouriteButton.parentNode.removeChild(favouriteButton);
            return response.text();
          }
          throw new Error('Request failed with status ' + response.status);
        })
        .then(data => {
          console.log(data);
        })
        .catch(error => {
          console.error(error);
        });
      });
    }

    visitCell.appendChild(favouriteButton);
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