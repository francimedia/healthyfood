Lungo.init({
    name: 'healthyfood'
});

var App = (function () {
    var self = {};
    // Global properties
    self.config = {
        apiURL: '/api/',
        priceURL: '/app/api/price.json'
    };
    // Global selectors
    self.$ = {
        map: $$('#map'),
        mapOverlay: $$('#map-overlay'),
        btnMapClose: $$('#map-close'),
        navCalendar: $$('#calendar-nav a')
    };

    self.addSubpageLinks = function () {
        var links = $$('.calendar-layout a');
        links.data('router', 'section');
        links.attr('href', '#subpage');
    };

    return self;
})();

// Starting point of app (on ready)
$$(function () {
    // Start map and bind it's events
    (function initMap() {
        var map = App.Map;
        map.init();
        App.$.mapOverlay.on('tap', map.open);
        App.$.btnMapClose.on('tap', map.close);
        App.addSubpageLinks();
    })();

    // Bind menu for map calendar nav
    App.$.navCalendar.on('tap', App.Map.menu);


    // Set venue list height based on window height

    App.winHeight = window.innerHeight;
    App.winWidth = window.innerWidth;
    
    var headerHeight = 44;
    // Default mobile height
    App.mapHeight = 148;

    if(App.winWidth >= 768){
        App.mapHeight = 320;
    }

    var offset = headerHeight + App.mapHeight;
    App.contentHeight = (App.winHeight - offset);


    
    $$('.calendar-layout ').css('height', App.contentHeight + 'px');
    // Home and Venue map
    $$('.map, #map-canvas').css('height', App.mapHeight + 'px');


    App.gMap.initialize();

});
