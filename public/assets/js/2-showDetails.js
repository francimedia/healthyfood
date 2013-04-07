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
    var data = App.Details.data;

    var $price = $$('.price');
    // Clear prices for fields onload
    $price.val('');


    $$('#subpage').on('load', function (e) {

    });

    $$('#submit-price').on('tap', function () {
        // Add price for item
        data.price = $price.val();
        Lungo.Service.post(App.config.priceURL, data, App.Details.parseResponse, "json")
    });

    // Use only one price when sending
    $price.on('change', function(){
        if($$(this).hasClass('per-pound')){
            $$('.single').val('');
        }
        if($$(this).hasClass('single')){
            $$('.per-pound').val('');
        }
    })


});