App.gMap = function() {
	'use strict';
	var gMap = {};

	gMap.currentPosition;


	gMap.calcRoute = function(orgin, dest) {

		var mapOrgin  = new google.maps.LatLng(orgin.lat, orgin.lon);
		var mapDest  = new google.maps.LatLng(dest.lat, dest.lon);
		var request = {
			origin: mapOrgin,
			destination: mapDest,
			travelMode: google.maps.TravelMode['WALKING']
		};

		var directionsDisplay = new google.maps.DirectionsRenderer();
		var directionsService = new google.maps.DirectionsService();

		var mapOptions = {
			zoom: 14,
			zoomControl: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: mapOrgin
		}
		var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
		directionsDisplay.setMap(map);

		directionsService.route(request, function(response, status) {
			if (status == google.maps.DirectionsStatus.OK) {
				directionsDisplay.setDirections(response);
			}
		});
		map.setCenter(mapOrgin);
	};
	return gMap;
}();