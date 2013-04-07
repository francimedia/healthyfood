App.Details = (function () {
    var self = {};
    self.data = {};

    self.setVenueID = function(id){
        self.data.venue_id = id;
    };

    self.parseResponse = function(){
        console.log('Price sent!');
    };

    return self;
})();

$$(function () {
    // Shortcut
    var data = App.Details.data;
    $$('#subpage').on('load', function (e) {

    });

    $$('#submit-price').on('tap', function () {
        // Add price for item
        data.price = $$('.price').val();
        console.log(data);
        Lungo.Service.post(App.config.priceURL, data, App.Details.parseResponse, "json")
    });
});