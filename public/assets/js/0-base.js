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
    var headerHeight = 44,
        mapHeight = 148,
        offset = headerHeight + mapHeight;

    var body = document.body,
        html = document.documentElement;
    var winHeight = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight );
    App.winHeight = winHeight;
    App.contentHeight = (winHeight - offset);

    $$('.calendar-layout ').css('height', App.contentHeight + 'px');
    // console.log(winHeight);

});
