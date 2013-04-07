App.Details = (function () {
    var self = {};
    self.data = {};

    self.setVenueData = function (data) {
        self.data.name = data.name;
        self.data.street = data.street;
        self.data.distance = data.distance;
        self.data.venue_id = data.venueID;
    };

    self.parseResponse = function () {
        var html = '<b>Thank you!</b>';
        $$('.price-response').html(html);
    };

    return self;
})();

$$(function () {
    var data = App.Details.data,
        $price = $$('.per-pound');
    Lungo.dom('#subpage').on('load', function () {
        // Clear fields
        $price.val('');
        $$('.locationDetail').text('');
        $$('.store-title').text(data.name);
        $$('.store-location').text(data.street);
        $$('.store-distance').text(data.distance);

    })

    $$('#submit-price').on('tap', function () {
        var price = $$('.per-pound').val() ? $$('.per-pound').val() : $$('.single').val();
        var sendData = {
            venue_id: data.venue_id,
            price: price
        }
        console.log(sendData);
        Lungo.Service.post(App.config.priceURL, sendData, App.Details.parseResponse, "json")
    });

    // Use only one price when sending
    $price.on('change', function () {
        if ($$(this).hasClass('per-pound')) {
            $$('.single').val('');
        }
        if ($$(this).hasClass('single')) {
            $$('.per-pound').val('');
        }
    })


});