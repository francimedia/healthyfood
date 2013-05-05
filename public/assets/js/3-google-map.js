App.gMap = function() {
	'use strict';
	var gMap = {};

	var directionsDisplay;
	var directionsService = new google.maps.DirectionsService();
	var map;
	var haight = new google.maps.LatLng(37.7699298, -122.4469157);
	var oceanBeach = new google.maps.LatLng(37.7683909618184, -122.51089453697205);

	gMap.initialize = function() {
		directionsDisplay = new google.maps.DirectionsRenderer();
		var mapOptions = {
			zoom: 14,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: haight
		}
		map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
		directionsDisplay.setMap(map);
	}

	gMap.calcRoute = function(org, dest) {
		var request = {
			origin: org,
			destination: dest,
			// Note that Javascript allows us to access the constant
			// using square brackets and a string value as its
			// "property."
			travelMode: google.maps.TravelMode['WALKING']
		};
		directionsService.route(request, function(response, status) {
			if (status == google.maps.DirectionsStatus.OK) {
				directionsDisplay.setDirections(response);
			}
		});
	}

	return gMap;
}();