var directionsService;
var directionsRenderer;
var placesService;

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
    placesService = new google.maps.places.PlacesService(map);
    var path = response.routes[0].overview_path;
    var promises = [];
    var step = path.length / 10;
    for (let i = 0, timeout = 0; i < path.length; i += step, timeout++) {
      promises.push(nearbySearch(placesService, path[Math.floor(i)], timeout * 10));
    }
    Promise.all(promises)
        .then(function(results) { 
          document.getElementById("input-form").style.display = "none";
            var waypoints = [].concat(...results.filter(function(result){
              return result.length > 0;
            }));
            var uniqueWaypoints = filterUniqueWaypoints(waypoints);
            Dashboard(uniqueWaypoints, map);
        });
      };
    });
  });
};

function Dashboard(waypoints, map) {
  document.getElementById('dashboard').style.display = 'block';
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
    favouriteButton.innerHTML = "Add to favourites";
    visitButton.innerHTML = "Visit";

    var start = document.getElementById("start").value;
    var end = document.getElementById("end").value;

    visitButton.onclick = function() {
      if (visitedWaypoints.length < 7) {
        visitedWaypoints.push(waypoint);
        row.remove();
        updateRoute(start, end, visitedWaypoints, map);
      } else if (visitedWaypoints.length === 7) {
        visitedWaypoints.push(waypoint);
        row.remove();
        document.getElementById("dashboard").style.display = "none";
        updateRoute(start, end, visitedWaypoints, map);
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

function updateRoute(start, end, visitedWaypoints, map) {
  var waypoints = [];
  
  visitedWaypoints.forEach(function(waypoint) {
    waypoints.push({
      location: waypoint.geometry.location,
      stopover: true
    });
  });
  var request = {
    origin: start,
    destination: end,
    waypoints: waypoints,
    optimizeWaypoints: true,
    travelMode: "DRIVING"
  };
  console.log(waypoints);
  
  directionsService.route(request, function(response, status) {
    if (status === "OK") {
      directionsRenderer.setDirections(response);
      var link = document.getElementById("open-map-button");
      link.style.display = 'block';
      link.href = "https://www.google.com/maps/dir/?api=1&origin=" + start + "&destination=" + end + "&waypoints=" + response.routes[0].waypoint_order.map(function(index) {
        return visitedWaypoints[index].geometry.location;
      }).join("|");
      console.log(link.href);
      link.innerHTML = "Open in Google Maps";
      link.target = "_blank";
      link.addEventListener("click", function() {
        window.open(link.href, "_blank");
      });
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

function loadMapScript(){
  const script = document.createElement("script");
  script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCy_AMtwcEtaGdS7gbqDgIuUwN2UwDkf1k&libraries=places&callback=initMap';
  script.defer = true;
  script.async = true;

  document.body.appendChild(script);
}

window.onload = loadMapScript;
window.initMap = initMap;