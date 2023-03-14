var directionsService;
var directionsRenderer;
var placesService;

let start, end;
let visitFavAddress = null;
let freeTime;
const isAuthenticated = document.body.dataset.auth === "true";
const viewFavouritePlace = document.body.dataset.name;
const waypointsContainer = document.getElementById("waypoints");
const mapContainer = document.getElementById("map");

function initMap() {
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer();
    var startAutocomplete = new google.maps.places.Autocomplete(
        document.getElementById("start")
    );
    var endAutocomplete = new google.maps.places.Autocomplete(
        document.getElementById("end")
    );

    var map = new google.maps.Map(mapContainer, {
        zoom: 7.5,
        center: { lat: 42.725, lng: 25.483 },
    });
    directionsRenderer.setMap(map);

    if (viewFavouritePlace != "null") {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                document.getElementById("start").value =
                    position.coords.latitude + ", " + position.coords.longitude;
            });
        }
        document.getElementById("end").value = viewFavouritePlace;
    }

    document
        .getElementById("submit")
        .addEventListener("click", function (event) {
            event.preventDefault();
            getFreeTime();

            start = document.getElementById("start").value;
            end = document.getElementById("end").value;

            directionsService.route(
                {
                    origin: start,
                    destination: end,
                    travelMode: "DRIVING",
                },
                function (response, status) {
                    if (status === "OK") {
                        placesService = new google.maps.places.PlacesService(
                            map
                        );
                        directionsRenderer.setDirections(response);

                        if (freeTime != 0) {
                            document.getElementById(
                                "display-freeTime-text"
                            ).className = "";
                            document.getElementById(
                                "free-time-info"
                            ).className = "";
                            var duration = 0;
                            var legs = response.routes[0].legs;
                            for (var i = 0; i < legs.length; i++) {
                                duration += legs[i].duration.value;
                            }
                            const displayFreeTime =
                                document.getElementById("display-freeTime");
                            duration = Math.floor(duration / 60);
                            var freeTimeInHours = (freeTime - duration) / 60;
                            var hours = Math.floor(freeTimeInHours);
                            var minutes = Math.floor(
                                (freeTimeInHours - hours) * 60
                            );
                            displayFreeTime.innerHTML =
                                hours + " hours and " + minutes + " minutes";
                        }

                        var path = response.routes[0].overview_path;

                        let splitPathArray = splitPath(path);

                        splitPathArray.shift();
                        splitPathArray.pop();

                        let promises = [];

                        splitPathArray.forEach((path) => {
                            promises.push(nearbySearch(placesService, path));
                        });

                        Promise.all(promises).then(function (results) {
                            var waypoints = [].concat(
                                ...results.filter(function (result) {
                                    return result.length > 0;
                                })
                            );
                            var uniqueWaypoints =
                                filterUniqueWaypoints(waypoints);
                            Dashboard(uniqueWaypoints, map);
                        });
                    }
                }
            );
        });

    document
        .getElementById("current-location")
        .addEventListener("click", function (event) {
            event.preventDefault();
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    document.getElementById("start").value =
                        position.coords.latitude +
                        ", " +
                        position.coords.longitude;
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        });
}

function Dashboard(waypoints, map) {
    let visitedWaypoints = [];
    const visitedWaypointsContainer =
        document.getElementById("selected-waypoints");

    mapContainer.classList.remove("w-full");
    mapContainer.classList.add("w-3/5");
    document.querySelector("#results-container").classList.remove("hidden");

    waypoints.forEach(function (waypoint) {
        const buttonsArray = insertWaypoint(waypoint);

        const waypointContainer = buttonsArray[0];
        const visitButton = buttonsArray[1];
        let favouriteButton;
        if (isAuthenticated) {
            favouriteButton = buttonsArray[2];
            favouriteButton.onclick = function () {
                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");
                getUserId((userID) => {
                    fetch("/favourite-places", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        },
                        body: JSON.stringify({
                            user_id: userID,
                            place_id: waypoint.place_id,
                        }),
                    })
                        .then((response) => {
                            if (response.ok) {
                                favouriteButton.parentNode.removeChild(
                                    favouriteButton
                                );
                                return response.text();
                            }
                            throw new Error(
                                "Request failed with status " + response.status
                            );
                        })
                        .then((data) => {
                            console.log(data);
                        })
                        .catch((error) => {
                            console.error(error);
                        });
                });
            };
        }

        visitButton.onclick = function () {
            if (visitButton.innerText === "Remove") {
                visitedWaypoints = visitedWaypoints.filter(function (
                    visitedWaypoint
                ) {
                    return visitedWaypoint.place_id !== waypoint.place_id;
                });

                updateRoute(visitedWaypoints, map);
                waypointsContainer.appendChild(waypointContainer);
                visitButton.innerText = "Add to trip";
                return;
            }
            visitedWaypoints.push(waypoint);
            updateRoute(visitedWaypoints, map);
            visitedWaypointsContainer.after(waypointContainer);

            visitButton.innerText = "Remove";
        };
    });
}

function updateRoute(visitedWaypoints, map) {
    var waypoints = [];
    var duration = 0;

    visitedWaypoints.forEach(function (waypoint) {
        waypoints.push({
            location: waypoint.geometry.location,
            stopover: true,
        });
        duration += 30 * 60;
    });
    var request = {
        origin: start,
        destination: end,
        waypoints: waypoints,
        optimizeWaypoints: true,
        travelMode: "DRIVING",
    };

    directionsService.route(request, function (response, status) {
        if (status === "OK") {
            directionsRenderer.setDirections(response);
            var link = document.getElementById("open-map-button");
            link.href =
                "https://www.google.com/maps/dir/?api=1&origin=" +
                start +
                "&destination=" +
                end +
                "&waypoints=" +
                response.routes[0].waypoint_order
                    .map(function (index) {
                        return visitedWaypoints[index].geometry.location;
                    })
                    .join("|");
            link.innerHTML = "Open in Google Maps";
            link.target = "_blank";
            link.addEventListener("click", function () {
                window.open(link.href, "_blank");
            });
            if (freeTime != 0) {
                var legs = response.routes[0].legs;
                for (var i = 0; i < legs.length; i++) {
                    duration += legs[i].duration.value;
                }
                const displayFreeTime =
                    document.getElementById("display-freeTime");
                console.log(duration);
                duration = Math.floor(duration / 60);
                var freeTimeInHours = (freeTime - duration) / 60;
                var hours = Math.floor(freeTimeInHours);
                var minutes = Math.floor((freeTimeInHours - hours) * 60);
                displayFreeTime.innerHTML =
                    hours + " hours and " + minutes + " minutes";
            }
        }
    });
}

function nearbySearch(service, location, timeout) {
    return new Promise(function (resolve, reject) {
        setTimeout(function () {
            service.nearbySearch(
                {
                    location: location,
                    radius: 5000,
                    type: ["museum"],
                },
                function (results, status) {
                    if (status === "OK") {
                        resolve(results);
                    } else if (status === "ZERO_RESULTS") {
                        resolve([]);
                    }
                }
            );
        }, timeout);
    });
}

function getUserId(callback) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/get-user-id", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status == 200) {
            var userID = JSON.parse(xhr.responseText).id;
            callback(userID);
        }
    };
    xhr.send();
}

function filterUniqueWaypoints(waypoints) {
    var uniqueWaypoints = [];
    var uniqueIds = new Set();
    waypoints.forEach(function (waypoint) {
        if (!uniqueIds.has(waypoint.place_id)) {
            if (!waypoint.business_status.includes("CLOSED")) {
                uniqueIds.add(waypoint.place_id);
                uniqueWaypoints.push(waypoint);
            }
        }
    });
    return uniqueWaypoints;
}

function loadMapScript() {
    const script = document.createElement("script");
    script.src =
        "https://maps.googleapis.com/maps/api/js?key=AIzaSyBiF4hRN-HrbNOmmVCUc2p1I00FtrfAbao&libraries=places&callback=initMap";
    script.defer = true;
    script.async = true;

    document.body.appendChild(script);
}

window.onload = loadMapScript;
window.initMap = initMap;
function distance(point1, point2) {
    const lat1 = point1.lat();
    const lng1 = point1.lng();
    const lat2 = point2.lat();
    const lng2 = point2.lng();
    const earthRadius = 6371; // in kilometers
    const dLat = ((lat2 - lat1) * Math.PI) / 180;
    const dLng = ((lng2 - lng1) * Math.PI) / 180;
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos((lat1 * Math.PI) / 180) *
            Math.cos((lat2 * Math.PI) / 180) *
            Math.sin(dLng / 2) *
            Math.sin(dLng / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const d = earthRadius * c;
    return d;
}

function splitPath(path) {
    const totalDistance = path
        .slice(1)
        .reduce((acc, cur, i) => acc + distance(path[i], cur), 0);
    const segmentDistance = totalDistance / 10;
    const waypoints = [path[0]];
    let distanceSoFar = 0;
    for (let i = 0; i < path.length - 1; i++) {
        const d = distance(path[i], path[i + 1]);
        if (distanceSoFar + d >= segmentDistance) {
            const remainder = segmentDistance - distanceSoFar;
            const lat1 = path[i].lat();
            const lng1 = path[i].lng();
            const lat2 = path[i + 1].lat();
            const lng2 = path[i + 1].lng();
            let lat = lat1 + (lat2 - lat1) * (remainder / d);
            let lng = lng1 + (lng2 - lng1) * (remainder / d);

            lat = Number(lat);
            lng = Number(lng);

            waypoints.push({ lat, lng });
            path.splice(i + 1, 0, { lat: () => lat, lng: () => lng });
            distanceSoFar = 0;
        } else {
            distanceSoFar += d;
        }
    }
    waypoints.push(path[path.length - 1]);
    return waypoints;
}

function insertWaypoint(waypoint) {
    const waypointContainer = document.createElement("div");
    waypointContainer.className =
        "flex items-center justify-between w-full border";

    const waypointImage = document.createElement("img");
    waypointImage.className = "w-1/6 h-28 object-cover";
    if (waypoint.photos) {
        waypointImage.src = waypoint.photos[0].getUrl();
    } else {
        waypointImage.src = "https://via.placeholder.com/150";
    }

    const waypointInfoContainer = document.createElement("div");
    waypointInfoContainer.className = "flex justify-between p-2 w-full h-full";

    const NameAddressContainer = document.createElement("div");
    NameAddressContainer.className = "flex flex-col w-3/6";

    const waypointName = document.createElement("span");
    waypointName.className = "text-lg";
    waypointName.innerText = waypoint.name;

    const waypointAddress = document.createElement("span");
    waypointAddress.className = "text-sm";
    waypointAddress.innerText = waypoint.vicinity;

    const ButtonsContainer = document.createElement("div");
    ButtonsContainer.className = "flex items-center w-2/6";

    const addToTrip = document.createElement("button");
    addToTrip.className =
        "px-4 py-2 mr-1 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700";
    addToTrip.innerText = "Add to trip";

    let heartButton;
    if (isAuthenticated) {
        heartButton = document.createElement("button");
        heartButton.className = "text-3xl text-red-500";
        heartButton.innerHTML = "&hearts;";
        ButtonsContainer.appendChild(heartButton);
    }

    NameAddressContainer.appendChild(waypointName);
    NameAddressContainer.appendChild(waypointAddress);

    waypointInfoContainer.appendChild(NameAddressContainer);

    ButtonsContainer.appendChild(addToTrip);

    waypointInfoContainer.appendChild(ButtonsContainer);

    waypointContainer.appendChild(waypointImage);

    waypointContainer.appendChild(waypointInfoContainer);

    waypointsContainer.appendChild(waypointContainer);

    if (isAuthenticated) {
        return [waypointContainer, addToTrip, heartButton];
    } else {
        return [waypointContainer, addToTrip];
    }
}

function getFreeTime() {
    let freeTimeHours = parseInt(document.getElementById("hours").value) || 0;
    let freeTimeMinutes =
        parseInt(document.getElementById("minutes").value) || 0;
    freeTime = freeTimeHours * 60 + freeTimeMinutes;
}
