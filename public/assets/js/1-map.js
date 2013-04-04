App.Map = (function () {
    var self = {},
        $map = App.$.map,
        $overlay = App.$.mapOverlay,
        $btnCloseMap = App.$.btnMapClose;

    var userPosition;
    var m;

    var isOpen = false;
    /* Open/close the map
     * @param {String} action Accepts 'open' or 'close'
     * */
    function shutter(action) {
        if (typeof action !== 'string') {
            return;
        }

        function toggle(open) {
            $overlay.toggleClass('mhide');
            $btnCloseMap.toggleClass('mhide');
            $$('.cal-push').toggleClass('mapOpened');
            isOpen = open;
        }

        switch (action) {
            case 'open':
                if (!isOpen) {
                    toggle(true);

                    var anim = morpheus($$('.cal-push'), {
                        height: '420px', duration: 150
                        // , bezier: [[100, 200], [200, 100]]
                        , complete: function () {

                            if (userPosition) {
                                m.setSize({x: 320, y: 420});
                                m.center({
                                    lat: userPosition.coords.latitude,
                                    lon: userPosition.coords.longitude
                                }).zoom(13);
                            }

                        }
                    });

                }
                break;
            case 'close':
                if (isOpen) {

                    var anim = morpheus($$('.cal-push'), {
                        height: '52%', duration: 200
                        // , bezier: [[100, 200], [200, 100]]
                        , complete: function () {
                            m.setSize({x: 320, y: 120}).zoom(11);
                            toggle(false);
                        }
                    });


                }
                break;
            default:
        }
    }

    self.open = function () {
        shutter('open');
    };
    self.close = function () {
        shutter('close');
    };

    function startMap() {
        m = mapbox.map('map').zoom(9).center({ lat: 40.73269, lon: -73.99498 });
        m.addLayer(mapbox.layer().id('examples.map-4l7djmvo'));

        // Create an empty markers layer
        var markerLayer = mapbox.markers.layer();
        m.addLayer(markerLayer);

        // name: 'Madison Square Garden',
        // name: 'Clearview Cinemas Ziegfeld',

        markerLayer.features([
            {
                geometry: {
                    coordinates: [
                        -73.993671,
                        40.750346
                    ]
                },
                properties: {
                    'marker-size': 'small',
                    'marker-color': '#8aa924',
                    'marker-symbol': 1
                }
            },
            {
                geometry: {
                    coordinates: [
                        -73.979359,
                        40.763381
                    ]
                },
                properties: {
                    'marker-size': 'small',
                    'marker-color': '#607919',
                    'marker-symbol': 2
                }
            },
            {
                geometry: {
                    coordinates: [
                        -73.98983,
                        40.735486
                    ]
                },
                properties: {
                    'marker-size': 'small',
                    'marker-color': '#8aa924',
                    'marker-symbol': 3
                }
            },
            {
                geometry: {
                    coordinates: [
                        -74.015751,
                        40.709987
                    ]
                },
                properties: {
                    'marker-size': 'small',
                    'marker-color': '#607919',
                    'marker-symbol': 4
                }
            }
        ]);


        var geolocate = document.getElementById('geolocate');

        // This uses the HTML5 geolocation API, which is available on
        // most mobile browsers and modern browsers, but not in Internet Explorer
        //
        // See this chart of compatibility for details:
        // http://caniuse.com/#feat=geolocation
        if (!navigator.geolocation) {
            geolocate.innerHTML = 'geolocation is not available';
        } else {
            geolocate.onclick = function (e) {
                e.preventDefault();
                e.stopPropagation();
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        userPosition = position;
                        // Once we've got a position, zoom and center the map
                        // on it, add ad a single feature
                        m.zoom(11).center({
                            lat: position.coords.latitude,
                            lon: position.coords.longitude
                        });
                        markerLayer.add_feature({
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


                        // And hide the geolocation button
                        geolocate.parentNode.removeChild(geolocate);
                    },
                    function (err) {
                        // If the user chooses not to allow their location
                        // to be shared, display an error message.
                        geolocate.innerHTML = 'position could not be found';
                    });
            };
        }
    }

    self.init = function () {
        startMap();
    };

    self.menu = function (el) {
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


