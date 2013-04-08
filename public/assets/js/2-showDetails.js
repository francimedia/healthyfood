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
        Lungo.Notification.show('Thank you!', 'check', 2);
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
 
        $$('#submit-price').on('tap', function () {
            submitPriceForm();
        });

        // Use only one price when sending
        $price.on('change', function () {
            if ($$(this).hasClass('per-pound')) {
                $$('.single').val('');
            }
            if ($$(this).hasClass('single')) {
                $$('.per-pound').val('');
            }
        });
    });
});

function submitPriceForm() {
    var price = $$('.notification .per-pound').val() != 0 ? $$('.notification .per-pound').val() : $$('.notification .single').val();
    if(price == 0) {
        alert('Please provide a price!');
        return;
    }
    var sendData = {
        venue_id: App.Details.data.venue_id,
        price: price
    }
    console.log(sendData);
    Lungo.Service.post(App.config.priceURL, sendData, App.Details.parseResponse, "json");   
}