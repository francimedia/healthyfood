Lungo.init({
    name: 'healthyfood'
});

var App = (function () {
    var self = {};
    // Global properties
    self.config = {
        apiURL: '/api/'
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

});
