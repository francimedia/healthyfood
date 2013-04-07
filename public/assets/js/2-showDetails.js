App.Details = (function () {
    var self = {};
    self.data = {};

    self.setVenueData = function(data){
        self.data.name = data.name;
        self.data.street = data.street;
        self.data.distance = data.distance;
        self.data.venueID = data.venueID;
    };

    self.parseResponse = function(){
        var html = '<b>Thank you!</b>';
        $$('.price-response').html(html);
    };

    return self;
})();

$$(function () {
    var data = App.Details.data;

    var $price = $$('.price');


    Lungo.dom('#subpage').on('load', function(){
        // Clear fields
        $price.val('');
        $$('.locationDetail').text('');
        $$('.store-title').text(data.name);
        $$('.store-location').text(data.street);
        $$('.store-distance').text(data.distance);

    })

    $$('#submit-price').on('tap', function () {
        // Add price for item
        data.price = $price.val();
        console.log(data);
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