var venuesCache = [];

App.Map = (function() {
    var self = {},
    $map = App.$.map,
        $overlay = App.$.mapOverlay,
        $btnCloseMap = App.$.btnMapClose;

    var userPosition;
    var m;
    var geolocate = document.getElementById('geolocate');

    var isOpen = false;


    function mapZoomOut(zoom){
        // Once we've got a position, zoom and center the map
        // on it, add ad a single feature   
        m.setSize({
            x: App.winWidth,
            y: App.mapHeight
        }).center({
            lat: userPosition.coords.latitude,
            lon: userPosition.coords.longitude
        }).zoom(zoom);
    }


    /* Open/close the map
     * @param {String} action Accepts 'open' or 'close'
     * */    function shutter(action) {
        if (typeof action !== 'string') {
            return;
        }

        function toggle(open) {
            $overlay.toggleClass('mhide');
            $btnCloseMap.toggleClass('mhide');
            $$('.cal-push').toggleClass('mapOpened');
            isOpen = open;
        }

        
        var animationDuration = 150;
        switch (action) {
            case 'open':
                if (!isOpen) {
                    toggle(true);

                    var anim = morpheus($$('.cal-push'), {
                        height: App.winHeight + 'px',
                        duration: animationDuration
                        ,
                        complete: function() {
                            if (userPosition) {
                                m.setSize({
                                    x: App.winWidth,
                                    y: App.winHeight
                                });
                                m.center({
                                    lat: userPosition.coords.latitude,
                                    lon: userPosition.coords.longitude
                                }).zoom(14);
                            }
                        }
                    });

                }
                break;
            case 'close':
                if (isOpen) {

                    var anim = morpheus($$('.cal-push'), {
                        height: App.contentHeight + 'px',
                        duration: animationDuration,
                        complete: function() {
                            m.setSize({
                                x: App.winWidth,
                                y: App.mapHeight
                            }).zoom(13);
                            toggle(false);
                        }
                    });


                }
                break;
            default:
        }
    }

    self.open = function() {
        shutter('open');
    };
    self.close = function() {
        shutter('close');
    };

    function startMap() {
        var defaultLocation = {
            lat: 40.73269,
            lon: -73.99498
        };

        m = mapbox.map('map').zoom(11).center(defaultLocation);
        m.addLayer(mapbox.layer().id('examples.map-4l7djmvo'));


        // Create an empty markers layer
        var markerLayer = mapbox.markers.layer();
        m.addLayer(markerLayer);

        // getVenues(markerLayer, defaultLocation); 

        getUserPosition(markerLayer);

        // This uses the HTML5 geolocation API, which is available on
        // most mobile browsers and modern browsers, but not in Internet Explorer
        //
        // See this chart of compatibility for details:
        // http://caniuse.com/#feat=geolocation
        if (!navigator.geolocation) {
            geolocate.innerHTML = 'geolocation is not available';
        } else {
            geolocate.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                getUserPosition(markerLayer);
            };
        }
    }

    function getUserPosition(markerLayer) {
        navigator.geolocation.getCurrentPosition(

        function(position) {

            userPosition = position;
            mapZoomOut(13);

            var userMarkerLayer = mapbox.markers.layer();
            m.addLayer(userMarkerLayer);

            userMarkerLayer.add_feature({
                geometry: {
                    coordinates: [
                    position.coords.longitude,
                    position.coords.latitude]
                },
                properties: {
                    'marker-size': 'small',
                    'marker-color': '#4079ff',
                    'marker-symbol': 'circle',
                }
            });

            var tmpLocation = {
                lon: position.coords.longitude,
                lat: position.coords.latitude
            };

            getVenues(markerLayer, tmpLocation);

            // And hide the geolocation button 
            geolocate.parentNode.removeChild(geolocate);

            // App.gMap.initialize(position.coords);
        },

        function(err) {
            // If the user chooses not to allow their location
            // to be shared, display an error message.
            geolocate.innerHTML = 'position could not be found';
        });

    }

    function getVenues(markerLayer, userLocation) {
        Lungo.Element.loading("#cal-today", 1);
        var url = "/app/api/venues.json";
        var data = userLocation;

        var markerSymbols = {
            9: 'a',
            10: 'b',
            11: 'c',
            12: 'd',
            13: 'e',
            14: 'f',
            15: 'g',
            16: 'h',
            17: 'i',
            18: 'j',
            19: 'k',
            20: 'l'
        };

        var parseResponse = function(result) {
            venuesCache = [];
            // console.log(result);
            $$('#cal-today').html('<ul class="events-today"></ul>');
            var features = [];
            var $venues = $$('.events-today');
            $$.each(result.response.venues, function(index, venue) {
                venuesCache[venue.id] = venue;
                // console.log(index);
                // console.log(venue);
                var makerId = index < 9 ? index + 1 : markerSymbols[index];
                var markerColor = venue.save != 0 ? '#ff762c' : '#8aa924';
                features.push({
                    geometry: {
                        coordinates: [
                        venue.lon,
                        venue.lat]
                    },
                    properties: {
                        'marker-size': 'small',
                        'marker-color': markerColor,
                        'marker-symbol': makerId
                    }
                });

                // var mydata = venuesCache[id];

                var html = '<li class="accept"> \
                    <a href="#subpage" data-router="section" data-name="' + venue.name + 
                    '" data-street="' + venue.street + 
                    '" data-distance="' + venue.distance +
                    '" data-lat="' + venue.lat +
                    '" data-lon="' + venue.lon +
                    '" data-venueID="' + venue.id + '" > \
                        <div class="right" style="text-align: right">' + venue.distance + '';



                if (venue.save != 0) {
                    html += '<br><span style="color: #ff762c;">SAVE: ' + venue.save + '%</span>';
                }

                html += '</div> \
                        <strong>(' + makerId + ') ' + venue.name + '</strong> \
                        <small>' + venue.street + '</small> \
                    </a> \
                </li>';

                $venues.append(html);
            });

            // Append foursquare source:
            $venues.append('<li><div class="right" style="text-align: right"><img src="/assets/images/4sq_poweredby_16x16.png" alt="" /></div><small>Venue Data powered by</small><strong>Foursquare</strong></li>');
 

            $$('.calendar-layout a').on('tap', function() {

                var $this = $$(this);
                var data = {
                    name: $this.data('name'),
                    street: $this.data('street'),
                    distance: $this.data('distance'),
                    venueID: $this.data('venueID'),
                    dest: {
                        lat: $this.data('lat'),
                        lon: $this.data('lon'),
                    }
                    
                };

                // was this venue already rated?
                var ratedVenues = Lungo.Data.Storage.session("ratedVenues");
                // console.log(ratedVenues);

                if (ratedVenues == null) {
                    ratedVenues = [];
                }

                if (typeof ratedVenues[data.venueID] == 'undefined' || ratedVenues[data.venueID] != 1) {
                    Lungo.Notification.html($$('#price-form').html(), "See Venue");
                    ratedVenues[data.venueID] = 1;
                    Lungo.Data.Storage.session("ratedVenues", ratedVenues);
                }

                App.Details.setVenueData(data);

                // App.gMap.initialize();

                // Call Google maps for directions
                App.gMap.calcRoute(userLocation, data.dest);

            });

            markerLayer.features(features);
        };

        Lungo.Service.get(url, data, parseResponse, "json");

    }


    self.init = function() {
        startMap();
    };

    self.menu = function(el) {
        var el = el;
        // Show hide calendar content

        function updateCalendarContent(el) {
            var showContent = $$(el.srcElement).data('content');
            showContent = $$('#' + showContent);
            $$('.calendar-layout div').hide();
            showContent.show();
            // TODO: Make a call to update points on map
        }

        // Highlights selected menu item
        (function updateClass() {
            if (el.srcElement.className === 'active') {
                return;
            } else {
                // Remove class from others
                App.$.navCalendar.removeClass('active');

                if (el.srcElement.id === 'calendarFull') {
                    // The calendar needs more classes
                    el.srcElement.className = 'active icon calendar';
                } else {
                    el.srcElement.className = 'active';
                }
            }
            updateCalendarContent(el);
        })();
    };

    return self;
})();

$$(function() {

})